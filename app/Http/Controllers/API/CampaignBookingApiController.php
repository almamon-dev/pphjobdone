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
            'website_url'     => 'nullable|string|max:500',
            'target_keywords' => 'nullable|string',
        ]);

        $tier = CampaignTier::with('campaign.service')->findOrFail($request->campaign_tier_id);
        $service = $tier->campaign->service;

        $campaignDetails = $request->campaign_details ?? [];
        $websiteUrl = $request->website_url ?? ($campaignDetails['website_url'] ?? ($campaignDetails['links'] ?? null));
        $targetKeywords = $request->target_keywords ?? ($campaignDetails['target_keywords'] ?? ($campaignDetails['keywords'] ?? null));

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
            'campaign_details' => $campaignDetails,
            'website_url' => $websiteUrl,
            'target_keywords' => $targetKeywords,
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
                        'qualification_summary' => 'Converted Client - Placed Campaign Order BKG-' . $booking->id,
                        'status' => 'converted',
                    ]
                );
            } catch (\Exception $e) {
                Log::warning('Booking Lead Sync Warning: ' . $e->getMessage());
            }
        }

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
