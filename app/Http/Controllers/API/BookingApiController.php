<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
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
        $bookings = Booking::with(['service', 'pricingPlan', 'payments'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return $this->sendResponse($bookings, 'Bookings retrieved successfully.');
    }

    /**
     * Create a new booking and automatically get Payment Intent (Combined Flow)
     */
    public function store(Request $request)
    {
        $request->validate([
            'pricing_plan_id' => 'required|exists:pricing_plans,id',
        ]);

        $plan = \App\Models\PricingPlan::with('services')->findOrFail($request->pricing_plan_id);

        // 1. Create Booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'service_id' => $plan->services->first()?->id, // Fetching the first associated service
            'pricing_plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'price' => $plan->price,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $responseData = [
            'booking' => $booking,
            'client_secret' => null,
        ];

        // 2. Generate Stripe Client Secret immediately if price > 0
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
                    'automatic_payment_methods' => [
                        'enabled' => true,
                    ],
                ]);

                $responseData['client_secret'] = $intent->client_secret;
            } catch (\Exception $e) {
                Log::error('Stripe Intent Error: '.$e->getMessage());
                // Intent fail holeo booking return korchi jate dashboard theke retry kora jay
            }
        }

        return $this->sendResponse($responseData, 'Booking created successfully. Proceed to payment.');
    }

    /**
     * Get specific booking details
     */
    public function show($id)
    {
        $booking = Booking::with(['service', 'pricingPlan', 'payments'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return $this->sendResponse($booking, 'Booking details retrieved.');
    }

    /**
     * Get all payments for the authenticated user
     */
    public function paymentList()
    {
        $payments = Payment::whereHas('booking', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->with('booking')
            ->latest()
            ->get();

        return $this->sendResponse($payments, 'Payments retrieved successfully.');
    }

    /**
     * Create a Stripe PaymentIntent for a booking
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::where('user_id', Auth::id())->findOrFail($request->booking_id);

        if ($booking->payment_status === 'paid') {
            return $this->sendError('This booking is already paid.');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));

            $intent = PaymentIntent::create([
                'amount' => (int) ($booking->price * 100),
                'currency' => 'usd',
                'metadata' => [
                    'booking_id' => $booking->id,
                    'user_id' => Auth::id(),
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return $this->sendResponse([
                'client_secret' => $intent->client_secret,
                'payment_intent_id' => $intent->id,
            ], 'Stripe payment intent created.');
        } catch (\Exception $e) {
            Log::error('Stripe Error: '.$e->getMessage());

            return $this->sendError('Stripe Error: '.$e->getMessage());
        }
    }

    /**
     * Record/Verify a payment manually or generically
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

        // Check if transaction ID is already used
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
        } else {
            $booking->update(['payment_status' => 'failed']);
        }

        return $this->sendResponse($payment, 'Payment recorded and verified.');
    }

    /**
     * Unified Webhook endpoint
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        Log::info('Payment Webhook Received:', $payload);

        $bookingId = null;
        $transactionId = null;
        $amount = null;
        $status = 'failed';
        $currency = 'USD';
        $gateway = 'Gateway';

        // 1. Stripe Logic
        if (isset($payload['type'])) {
            $gateway = 'Stripe';
            $object = $payload['data']['object'];
            $event = $payload['type'];

            switch ($event) {
                case 'checkout.session.completed':
                case 'payment_intent.succeeded':
                case 'charge.succeeded':
                    $bookingId = $object['metadata']['booking_id'] ?? null;
                    $transactionId = $object['id'] ?? null;
                    $amount = isset($object['amount_total']) ? ($object['amount_total'] / 100) : (($object['amount_received'] ?? 0) / 100);
                    $status = 'paid';
                    $currency = strtoupper($object['currency'] ?? 'USD');
                    break;
                case 'payment_intent.payment_failed':
                    $bookingId = $object['metadata']['booking_id'] ?? null;
                    $transactionId = $object['id'] ?? null;
                    $status = 'failed';
                    break;
            }
        }
        // 2. Generic Fallback
        else {
            $bookingId = $request->input('booking_id');
            $transactionId = $request->input('transaction_id');
            $status = $request->input('payment_status', 'failed');
            $amount = $request->input('amount');
            $currency = $request->input('currency', 'USD');
            $gateway = 'Other';
        }

        if (! $bookingId) {
            return response()->json(['success' => false, 'message' => 'No booking id found'], 400);
        }

        $booking = Booking::find($bookingId);
        if (! $booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        $payment = Payment::updateOrCreate(
            ['transaction_id' => $transactionId],
            [
                'booking_id' => $booking->id,
                'amount' => $amount ?? $booking->price,
                'currency' => $currency,
                'payment_method' => $gateway,
                'status' => $status,
                'payment_payload' => $payload,
            ]
        );

        if (in_array(strtolower($status), ['paid', 'success', 'completed', 'succeeded'])) {
            $booking->update([
                'payment_status' => 'paid',
                'status' => 'ongoing',
            ]);
        } else {
            $booking->update(['payment_status' => 'failed']);
        }

        return response()->json(['success' => true]);
    }
}
