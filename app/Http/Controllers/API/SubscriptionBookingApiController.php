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
        ]);

        $plan = PricingPlan::with('services')->findOrFail($request->pricing_plan_id);
        $service = $plan->services->first();

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
        ]);

        $responseData = [
            'booking' => $booking,
            'client_secret' => null,
        ];

        // 2. Stripe Logic (One-Time Payment)
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
            } catch (\Exception $e) {
                Log::error('Stripe Payment Error: '.$e->getMessage());
            }
        }

        return $this->sendResponse($responseData, 'Subscription Booking created successfully.');
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
            
            if (!$targetSubscription) {
                return $this->sendError('Active subscription not found for this booking.');
            }

            $products = \Stripe\Product::all(['limit' => 100]);
            $stripeProduct = null;
            foreach($products->data as $p) {
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
            \Stripe\Subscription::update($targetSubscription->id, [
                'items' => [
                    [
                        'id' => $targetSubscription->items->data[0]->id,
                        'price_data' => [
                            'currency' => 'usd',
                            'product' => $stripeProduct->id,
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
                'pricing_plan_id' => $plan->id,
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
