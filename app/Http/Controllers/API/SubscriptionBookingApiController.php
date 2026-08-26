<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PricingPlan;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;

class SubscriptionBookingApiController extends Controller
{
    use ApiResponse;

    /**
     * Create a new subscription booking (recurring payment)
     */
    public function store(Request $request)
    {
        $request->validate([
            'pricing_plan_id' => 'required|exists:pricing_plans,id',
            'service_id'      => 'nullable|exists:services,id',
            'website_url'     => 'nullable|string|max:500',
            'target_keywords' => 'nullable|string',
        ]);

        $plan = PricingPlan::with('services')->findOrFail($request->pricing_plan_id);

        // Use user-selected service if provided, otherwise fall back to first linked service
        if ($request->service_id) {
            $service = $plan->services->firstWhere('id', $request->service_id) ?? $plan->services->first();
        } else {
            $service = $plan->services->first();
        }

        // 1. Create Booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'service_id' => $service?->id,
            'pricing_plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'price' => $plan->price,
            'status' => 'pending',
            'payment_status' => 'pending',
            'is_campaign' => false,
            'website_url' => $request->website_url,
            'target_keywords' => $request->target_keywords,
        ]);

        $responseData = [
            'booking' => $booking,
            'client_secret' => null,
        ];

        // Auto Sync Lead to CRM Database (Mark as Converted)
        $user = Auth::user();
        if ($user) {
            try {
                \App\Models\Lead::updateOrCreate(
                    ['email' => $user->email],
                    [
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'service_interest' => $booking->plan_name,
                        'budget' => '$' . $booking->price,
                        'qualification_status' => 'Hot',
                        'qualification_summary' => 'Converted Client - Placed Subscription Order BKG-' . $booking->id,
                        'status' => 'converted',
                    ]
                );
            } catch (\Exception $e) {
                Log::warning('Subscription Lead Sync Warning: ' . $e->getMessage());
            }
        }

        // 2. Stripe Logic (Recurring Subscription or PaymentIntent)
        if ($booking->price > 0) {
            try {
                Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));

                $customers = \Stripe\Customer::all(['email' => $user->email, 'limit' => 1]);

                if (count($customers->data) > 0) {
                    $stripeCustomer = $customers->data[0];
                } else {
                    $stripeCustomer = \Stripe\Customer::create([
                        'email' => $user->email,
                        'name' => $user->name,
                    ]);
                }

                $booking->update(['stripe_customer_id' => $stripeCustomer->id]);

                // Determine Stripe Price ID or create pricing parameters
                $priceId = $plan->stripe_price_id;

                if ($priceId) {
                    // Create Stripe Subscription using configured price ID
                    $subscription = \Stripe\Subscription::create([
                        'customer' => $stripeCustomer->id,
                        'items' => [
                            ['price' => $priceId],
                        ],
                        'payment_behavior' => 'default_incomplete',
                        'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
                        'expand' => ['latest_invoice.payment_intent'],
                        'metadata' => [
                            'booking_id' => $booking->id,
                            'user_id' => $user->id,
                        ],
                    ]);

                    $booking->update([
                        'stripe_subscription_id' => $subscription->id,
                        'stripe_status' => $subscription->status,
                        'current_period_end' => isset($subscription->current_period_end) ? date('Y-m-d H:i:s', $subscription->current_period_end) : null,
                        'cancel_at_period_end' => $subscription->cancel_at_period_end ?? false,
                    ]);

                    if (isset($subscription->latest_invoice->payment_intent->client_secret)) {
                        $responseData['client_secret'] = $subscription->latest_invoice->payment_intent->client_secret;
                    }
                } else {
                    // Fallback to PaymentIntent if plan has no stripe_price_id yet
                    $paymentIntent = \Stripe\PaymentIntent::create([
                        'amount' => (int) ($booking->price * 100),
                        'currency' => 'usd',
                        'customer' => $stripeCustomer->id,
                        'description' => 'Payment for ' . $plan->name,
                        'metadata' => [
                            'booking_id' => $booking->id,
                            'user_id' => $user->id,
                        ],
                    ]);

                    $responseData['client_secret'] = $paymentIntent->client_secret;
                }
            } catch (\Exception $e) {
                Log::error('Stripe Subscription Error: ' . $e->getMessage());
            }
        }

        return $this->sendResponse($responseData, 'Subscription Booking created successfully.');
    }

    /**
     * Fetch authenticated user's current subscription details & auto-renewal state
     */
    public function getUserSubscription(Request $request)
    {
        $userId = Auth::id();

        // Get latest subscription booking for user
        $booking = Booking::with('pricingPlan')
            ->where('user_id', $userId)
            ->where('is_campaign', false)
            ->whereNotNull('pricing_plan_id')
            ->latest()
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => true,
                'has_subscription' => false,
                'data' => null,
                'message' => 'No active subscription found.',
            ]);
        }

        $stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET');

        // Live Sync with Stripe if stripe_subscription_id is attached
        if ($booking->stripe_subscription_id && $stripeSecret) {
            try {
                Stripe::setApiKey($stripeSecret);
                $stripeSub = \Stripe\Subscription::retrieve($booking->stripe_subscription_id);

                if ($stripeSub) {
                    $booking->update([
                        'stripe_status' => $stripeSub->status,
                        'current_period_end' => isset($stripeSub->current_period_end) ? date('Y-m-d H:i:s', $stripeSub->current_period_end) : $booking->current_period_end,
                        'cancel_at_period_end' => (bool) ($stripeSub->cancel_at_period_end ?? false),
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Stripe Subscription retrieve error: ' . $e->getMessage());
            }
        }

        $renewalDate = $booking->current_period_end 
            ? $booking->current_period_end->format('F d, Y')
            : ($booking->created_at ? $booking->created_at->addMonth()->format('F d, Y') : null);

        $subscriptionData = [
            'booking_id' => $booking->id,
            'plan_id' => $booking->pricing_plan_id,
            'plan_name' => $booking->plan_name ?? $booking->pricingPlan?->name ?? 'Standard Package',
            'price' => '$' . number_format((float) $booking->price, 2),
            'raw_price' => (float) $booking->price,
            'billing_interval' => $booking->pricingPlan?->billing_interval ?? 'month',
            'status' => $booking->stripe_status ?? ($booking->payment_status === 'paid' ? 'active' : 'pending'),
            'payment_status' => $booking->payment_status,
            'stripe_subscription_id' => $booking->stripe_subscription_id,
            'current_period_end' => $booking->current_period_end ? $booking->current_period_end->toIso8601String() : null,
            'formatted_renewal_date' => $renewalDate,
            'cancel_at_period_end' => (bool) $booking->cancel_at_period_end,
            'auto_renew' => !(bool) $booking->cancel_at_period_end,
            'created_at' => $booking->created_at ? $booking->created_at->toIso8601String() : null,
        ];

        return response()->json([
            'success' => true,
            'has_subscription' => true,
            'data' => $subscriptionData,
            'message' => 'User subscription fetched successfully.',
        ]);
    }

    /**
     * Toggle auto-renewal ON or OFF for user's subscription
     */
    public function toggleAutoRenewal(Request $request)
    {
        $userId = Auth::id();

        $booking = Booking::where('user_id', $userId)
            ->where('is_campaign', false)
            ->whereNotNull('pricing_plan_id')
            ->latest()
            ->firstOrFail();

        // Target auto-renew state: if requested auto_renew boolean is supplied, use it; otherwise toggle current
        $shouldAutoRenew = $request->has('auto_renew') 
            ? (bool) $request->input('auto_renew')
            : (bool) $booking->cancel_at_period_end; // Toggle

        $cancelAtPeriodEnd = !$shouldAutoRenew;

        $stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET');

        if ($booking->stripe_subscription_id && $stripeSecret) {
            try {
                Stripe::setApiKey($stripeSecret);
                $stripeSub = \Stripe\Subscription::update($booking->stripe_subscription_id, [
                    'cancel_at_period_end' => $cancelAtPeriodEnd,
                ]);

                $cancelAtPeriodEnd = (bool) $stripeSub->cancel_at_period_end;
                $currentPeriodEnd = isset($stripeSub->current_period_end) ? date('Y-m-d H:i:s', $stripeSub->current_period_end) : $booking->current_period_end;

                $booking->update([
                    'cancel_at_period_end' => $cancelAtPeriodEnd,
                    'current_period_end' => $currentPeriodEnd,
                    'stripe_status' => $stripeSub->status,
                ]);
            } catch (\Exception $e) {
                Log::error('Stripe Toggle Auto-Renew Error: ' . $e->getMessage());
                return $this->sendError('Stripe sync failed: ' . $e->getMessage());
            }
        } else {
            // Local update fallback if no stripe_subscription_id yet
            $booking->update([
                'cancel_at_period_end' => $cancelAtPeriodEnd,
            ]);
        }

        $formattedDate = $booking->current_period_end 
            ? $booking->current_period_end->format('F d, Y')
            : ($booking->created_at ? $booking->created_at->addMonth()->format('F d, Y') : 'the end of billing period');

        $message = !$cancelAtPeriodEnd
            ? "Auto-renewal turned ON. Your subscription will renew on {$formattedDate}."
            : "Auto-renewal turned OFF. Your subscription will remain active until {$formattedDate}.";

        return response()->json([
            'success' => true,
            'auto_renew' => !$cancelAtPeriodEnd,
            'cancel_at_period_end' => $cancelAtPeriodEnd,
            'formatted_renewal_date' => $formattedDate,
            'message' => $message,
        ]);
    }

    /**
     * Upgrade or Downgrade a subscription package
     */
    public function upgrade(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'pricing_plan_id' => 'required|exists:pricing_plans,id',
        ]);

        $booking = Booking::where('user_id', Auth::id())->where('is_campaign', false)->findOrFail($request->booking_id);

        $plan = PricingPlan::with('services')->findOrFail($request->pricing_plan_id);
        $planName = $plan->name;
        $price = $plan->price;

        try {
            Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));

            $user = Auth::user();
            $customers = \Stripe\Customer::all(['email' => $user->email, 'limit' => 1]);

            if (count($customers->data) == 0) {
                return $this->sendError('Stripe customer not found.');
            }

            $stripeCustomer = $customers->data[0];
            $subscriptions = \Stripe\Subscription::all(['customer' => $stripeCustomer->id]);
            $targetSubscription = null;

            foreach ($subscriptions->data as $sub) {
                if (isset($sub->metadata->booking_id) && $sub->metadata->booking_id == $booking->id) {
                    $targetSubscription = $sub;
                    break;
                }
            }

            if (!$targetSubscription && $booking->stripe_subscription_id) {
                try {
                    $targetSubscription = \Stripe\Subscription::retrieve($booking->stripe_subscription_id);
                } catch (\Exception $e) {
                    Log::warning('Subscription retrieve error: ' . $e->getMessage());
                }
            }

            if (!$targetSubscription) {
                return $this->sendError('Active subscription not found for this booking.');
            }

            $products = \Stripe\Product::all(['limit' => 100]);
            $stripeProduct = null;
            foreach ($products->data as $p) {
                if ($p->name === $planName) {
                    $stripeProduct = $p;
                    break;
                }
            }
            if (!$stripeProduct) {
                $stripeProduct = \Stripe\Product::create([
                    'name' => $planName,
                ]);
            }

            // Update the subscription item with the new price
            $updatedSub = \Stripe\Subscription::update($targetSubscription->id, [
                'items' => [
                    [
                        'id' => $targetSubscription->items->data[0]->id,
                        'price_data' => [
                            'currency' => 'usd',
                            'product' => $stripeProduct->id,
                            'unit_amount' => (int) ($price * 100),
                            'recurring' => [
                                'interval' => $plan->billing_interval ?? 'month',
                            ],
                        ],
                    ],
                ],
                'proration_behavior' => 'create_prorations',
            ]);

            // Update local booking record
            $booking->update([
                'pricing_plan_id' => $plan->id,
                'plan_name' => $planName,
                'price' => $price,
                'current_period_end' => isset($updatedSub->current_period_end) ? date('Y-m-d H:i:s', $updatedSub->current_period_end) : $booking->current_period_end,
            ]);

            return $this->sendResponse($booking, 'Package updated successfully.');
        } catch (\Exception $e) {
            Log::error('Stripe Upgrade Error: ' . $e->getMessage());
            return $this->sendError('Failed to upgrade package: ' . $e->getMessage());
        }
    }
}
