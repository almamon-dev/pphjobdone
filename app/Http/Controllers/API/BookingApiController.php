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
            'pricing_plan_id' => 'required_without:campaign_tier_id|exists:pricing_plans,id',
            'campaign_tier_id' => 'required_without:pricing_plan_id|exists:campaign_tiers,id',
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
            'is_payment' => false,
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
                $intent = PaymentIntent::create([
                    'amount' => (int) ($booking->price * 100),
                    'currency' => 'usd',
                    'metadata' => [
                        'booking_id' => $booking->id,
                        'user_id' => Auth::id(),
                    ],
                    'automatic_payment_methods' => ['enabled' => true],
                ]);
                $responseData['client_secret'] = $intent->client_secret;
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
                'is_payment' => true,
                'status' => 'ongoing',
            ]);
            $booking->user->update(['is_subscribed' => true]);
        }

        return $this->sendResponse($payment, 'Payment recorded and verified.');
    }
}
