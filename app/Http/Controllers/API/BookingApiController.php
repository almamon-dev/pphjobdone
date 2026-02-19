<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookingApiController extends Controller
{
    use ApiResponse;

    /**
     * Get all bookings for the authenticated user
     */
    public function index()
    {
        $bookings = Booking::with(['service', 'payments'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return $this->sendResponse($bookings, 'Bookings retrieved successfully.');
    }

    /**
     * Create a new booking (Call this from React "Complete Payment" button)
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'plan_name' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'service_id' => $request->service_id,
            'plan_name' => $request->plan_name,
            'price' => $request->price,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        return $this->sendResponse($booking, 'Booking created successfully. Proceed to payment.');
    }

    /**
     * Get specific booking details
     */
    public function show($id)
    {
        $booking = Booking::with(['service', 'payments'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return $this->sendResponse($booking, 'Booking details retrieved.');
    }

    public function webhook(Request $request)
    {
        $payload = $request->all();
        Log::info('Payment Webhook Received:', $payload);

        // 1. Handle Stripe Style (Webhooks)
        if (isset($payload['type'])) {
            $event = $payload['type'];
            $object = $payload['data']['object'];

            switch ($event) {
                case 'checkout.session.completed':
                case 'payment_intent.succeeded':
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
                    $amount = ($object['amount_received'] ?? 0) / 100;
                    $currency = strtoupper($object['currency'] ?? 'USD');
                    break;

                default:
                    Log::info("Unhandled Stripe Event Type: {$event}");

                    return response()->json(['success' => true, 'message' => 'Event ignored']);
            }
        }
        // 2. Handle SSLCommerz / Generic Style
        else {
            $bookingId = $request->input('booking_id') ?? $request->input('value_a');
            $transactionId = $request->input('transaction_id') ?? $request->input('tran_id');
            $status = $request->input('payment_status') ?? $request->input('status');
            $amount = $request->input('amount') ?? $request->input('amount_paid');
            $currency = $request->input('currency', 'USD');
        }

        $booking = Booking::find($bookingId);

        if (! $booking) {
            Log::error('Webhook Error: Booking not found.', ['booking_id' => $bookingId, 'payload' => $payload]);

            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }
        // Record the payment
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'transaction_id' => $transactionId,
            'amount' => $amount ?? $booking->price,
            'currency' => $currency,
            'payment_method' => $request->input('payment_method', 'Gateway'),
            'status' => $status,
            'payment_payload' => $payload,
        ]);

        // Update Booking Status
        if (in_array(strtolower($status), ['paid', 'success', 'completed', 'succeeded'])) {
            $booking->update([
                'payment_status' => 'paid',
                'status' => 'ongoing',
            ]);
            Log::info("Payment Successful for Booking #{$booking->id}");
        } else {
            $booking->update(['payment_status' => 'failed']);
            Log::warning("Payment Failed for Booking #{$booking->id}");
        }

        return response()->json(['success' => true, 'message' => 'Webhook processed']);
    }
}
