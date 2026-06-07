<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CampaignTier;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;

class CampaignBookingApiController extends Controller
{
    use ApiResponse;

    /**
     * Create a new campaign booking (one-time payment)
     */
    public function store(Request $request)
    {
        $request->validate([
            'campaign_tier_id' => 'required|exists:campaign_tiers,id',
            'campaign_details' => 'nullable|array',
        ]);

        $tier = CampaignTier::with('campaign.service')->findOrFail($request->campaign_tier_id);
        $service = $tier->campaign->service;

        // 1. Create Booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'service_id' => $service?->id,
            'campaign_tier_id' => $tier->id,
            'plan_name' => $tier->campaign->title . " ($" . $tier->price . ")",
            'price' => $tier->price,
            'status' => 'pending',
            'payment_status' => 'pending',
            'is_campaign' => true,
            'campaign_details' => $request->campaign_details,
        ]);

        $responseData = [
            'booking' => $booking,
            'client_secret' => null,
        ];

        // 2. Stripe Logic (One-Time PaymentIntent)
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
                    'metadata' => [
                        'booking_id' => $booking->id,
                        'user_id' => $user->id,
                    ],
                ]);

                $responseData['client_secret'] = $paymentIntent->client_secret;
            } catch (\Exception $e) {
                Log::error('Stripe Campaign Payment Error: '.$e->getMessage());
            }
        }

        return $this->sendResponse($responseData, 'Campaign Booking created successfully.');
    }
}
