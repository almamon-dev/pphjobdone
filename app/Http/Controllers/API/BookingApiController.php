<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\CampaignTier;
use App\Models\PricingPlan;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class BookingApiController extends Controller
{
    use ApiResponse;

    /**
     * Get all bookings for the authenticated user
     */
    public function index()
    {
        $bookings = Booking::with(['service', 'pricingPlan', 'campaignTier.campaign', 'payments'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return $this->sendResponse($bookings, 'Bookings retrieved successfully.');
    }

    /**
     * Create a new booking
     */
    public function store(Request $request)
    {
        $request->validate([
            'pricing_plan_id' => 'required_without:campaign_tier_id|nullable|exists:pricing_plans,id',
            'campaign_tier_id' => 'required_without:pricing_plan_id|nullable|exists:campaign_tiers,id',
            'campaign_details' => 'nullable|array',
        ]);

        if ($request->campaign_tier_id) {
            $tier = CampaignTier::with('campaign.service')->findOrFail($request->campaign_tier_id);
            $service = $tier->campaign->service;
            $planId = null;
            $campaignTierId = $tier->id;
            $planName = $tier->campaign->title . " ($" . $tier->price . ")";
            $price = $tier->price;
            $isCampaign = true;
        } else {
            $plan = PricingPlan::with('services')->findOrFail($request->pricing_plan_id);
            $service = $plan->services->first();
            $planId = $plan->id;
            $campaignTierId = null;
            $planName = $plan->name;
            $price = $plan->price;
            $isCampaign = false;
        }

        // 1. Create Booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'service_id' => $service?->id,
            'pricing_plan_id' => $planId,
            'campaign_tier_id' => $campaignTierId,
            'plan_name' => $planName,
            'price' => $price,
            'status' => 'pending',
            'payment_status' => 'pending',
            'is_campaign' => $isCampaign,
            'campaign_details' => $request->campaign_details,
        ]);

        $responseData = [
            'booking' => $booking,
            'client_secret' => null,
        ];

        // 2. Stripe Logic
        if ($booking->price > 0) {
            try {
                Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));
                
                $user = Auth::user();
                $customers = \Stripe\Customer::all(['email' => $user->email, 'limit' => 1]);
                
                if (count($customers->data) > 0) {
                    $stripeCustomer = $customers->data[0];
                } else {
                    $stripeCustomer = \Stripe\Customer::create([
                        'email' => $user->email,
                        'name' => $user->name,
                    ]);
                }

                $subscription = \Stripe\Subscription::create([
                    'customer' => $stripeCustomer->id,
                    'items' => [
                        [
                            'price_data' => [
                                'currency' => 'usd',
                                'product_data' => [
                                    'name' => $planName,
                                ],
                                'unit_amount' => (int) ($booking->price * 100),
                                'recurring' => [
                                    'interval' => 'month',
                                ],
                            ],
                        ],
                    ],
                    'payment_behavior' => 'default_incomplete',
                    'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
                    'expand' => ['latest_invoice.payment_intent'],
                    'metadata' => [
                        'booking_id' => $booking->id,
                        'user_id' => $user->id,
                    ],
                ]);

                $responseData['client_secret'] = $subscription->latest_invoice->payment_intent->client_secret;
            } catch (\Exception $e) {
                Log::error('Stripe Error: '.$e->getMessage());
            }
        }

        return $this->sendResponse($responseData, 'Booking created successfully.');
    }

    /**
     * Get specific booking details
     */
    public function show($id)
    {
        $booking = Booking::with(['service', 'pricingPlan', 'campaignTier.campaign', 'payments'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return $this->sendResponse($booking, 'Booking details retrieved.');
    }

    /**
     * Verify payment status
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'transaction_id' => 'required|string',
            'amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'status' => 'required|string',
        ]);

        $booking = Booking::where('user_id', Auth::id())->findOrFail($request->booking_id);

        if (Payment::where('transaction_id', $request->transaction_id)->exists()) {
            return $this->sendError('This transaction ID has already been recorded.');
        }

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'transaction_id' => $request->transaction_id,
            'amount' => $request->amount,
            'currency' => $request->input('currency', 'USD'),
            'payment_method' => $request->payment_method,
            'status' => $request->status,
            'payment_payload' => $request->all(),
        ]);

        if (in_array(strtolower($request->status), ['paid', 'success', 'completed', 'succeeded'])) {
            $booking->update([
                'payment_status' => 'paid',
                'status' => 'ongoing',
            ]);
            $booking->user->update(['is_subscribed' => true]);
        }
        return $this->sendResponse($payment, 'Payment recorded and verified.');
    }

    /**
     * Handle Stripe Webhooks (for recurring payments)
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            if ($endpointSecret) {
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            } else {
                // Fallback if no webhook secret is set (less secure, but works for testing)
                $event = \Stripe\Event::constructFrom(json_decode($payload, true));
            }
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                
                // If it's a subscription invoice
                if ($invoice->subscription) {
                    try {
                        Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));
                        $subscription = \Stripe\Subscription::retrieve($invoice->subscription);
                        
                        $bookingId = $subscription->metadata->booking_id ?? null;
                        
                        if ($bookingId) {
                            $booking = Booking::find($bookingId);
                            if ($booking) {
                                // Check if this transaction was already recorded (e.g. first payment via verifyPayment)
                                $transactionId = $invoice->payment_intent;
                                if (!Payment::where('transaction_id', $transactionId)->exists()) {
                                    Payment::create([
                                        'booking_id' => $booking->id,
                                        'transaction_id' => $transactionId,
                                        'amount' => $invoice->amount_paid / 100,
                                        'currency' => strtoupper($invoice->currency),
                                        'payment_method' => 'Stripe Subscription',
                                        'status' => 'succeeded',
                                        'payment_payload' => $invoice->toArray(),
                                    ]);
                                    
                                    $booking->update([
                                        'payment_status' => 'paid',
                                    ]);
                                    $booking->user->update(['is_subscribed' => true]);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Webhook Subscription Error: ' . $e->getMessage());
                    }
                }
                break;
                
            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                $bookingId = $subscription->metadata->booking_id ?? null;
                if ($bookingId) {
                    $booking = Booking::find($bookingId);
                    if ($booking) {
                        $booking->user->update(['is_subscribed' => false]);
                        // Optionally update booking status if needed
                    }
                }
                break;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Upgrade or Downgrade a booking's package
     */
    public function upgrade(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'pricing_plan_id' => 'required_without:campaign_tier_id|nullable|exists:pricing_plans,id',
            'campaign_tier_id' => 'required_without:pricing_plan_id|nullable|exists:campaign_tiers,id',
        ]);

        $booking = Booking::where('user_id', Auth::id())->findOrFail($request->booking_id);

        if ($request->campaign_tier_id) {
            $tier = CampaignTier::with('campaign.service')->findOrFail($request->campaign_tier_id);
            $planId = null;
            $campaignTierId = $tier->id;
            $planName = $tier->campaign->title . " ($" . $tier->price . ")";
            $price = $tier->price;
        } else {
            $plan = PricingPlan::with('services')->findOrFail($request->pricing_plan_id);
            $planId = $plan->id;
            $campaignTierId = null;
            $planName = $plan->name;
            $price = $plan->price;
        }

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
            
            if (!$targetSubscription) {
                return $this->sendError('Active subscription not found for this booking.');
            }

            // Update the subscription item with the new price
            \Stripe\Subscription::update($targetSubscription->id, [
                'items' => [
                    [
                        'id' => $targetSubscription->items->data[0]->id,
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $planName,
                            ],
                            'unit_amount' => (int) ($price * 100),
                            'recurring' => [
                                'interval' => 'month',
                            ],
                        ],
                    ],
                ],
                'proration_behavior' => 'create_prorations',
            ]);

            // Update local booking record
            $booking->update([
                'pricing_plan_id' => $planId,
                'campaign_tier_id' => $campaignTierId,
                'plan_name' => $planName,
                'price' => $price,
            ]);

            return $this->sendResponse($booking, 'Package updated successfully.');

        } catch (\Exception $e) {
            Log::error('Stripe Upgrade Error: '.$e->getMessage());
            return $this->sendError('Failed to upgrade package: ' . $e->getMessage());
        }
    }
}
