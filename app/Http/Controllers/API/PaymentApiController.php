<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;

class PaymentApiController extends Controller
{
    use ApiResponse;

    /**
     * Get all payments for the authenticated user (Transactions List)
     */
    public function index(Request $request)
    {
        $payments = Payment::with(['booking.pricingPlan', 'booking.campaignTier.campaign'])
            ->whereHas('booking', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest()
            ->get();

        return $this->sendResponse($payments, 'Payment history retrieved successfully.');
    }



    /**
     * Handle Stripe Webhooks (for recurring payments)
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        
        Log::info('Webhook Received', ['sigHeader' => $sigHeader, 'has_secret' => (bool)$endpointSecret]);

        try {
            if ($endpointSecret) {
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            } else {
                // Fallback if no webhook secret is set
                $event = \Stripe\Event::constructFrom(json_decode($payload, true));
            }
        } catch (\UnexpectedValueException $e) {
            Log::error('Webhook Error: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Webhook Error: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Webhook Event Type: ' . $event->type);

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
                    }
                }
                break;
                
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                
                // Exclude invoice-generated payment intents (they are handled by invoice.payment_succeeded)
                if (empty($paymentIntent->invoice)) {
                    $bookingId = $paymentIntent->metadata->booking_id ?? null;
                    
                    if ($bookingId) {
                        $booking = Booking::find($bookingId);
                        if ($booking) {
                            $transactionId = $paymentIntent->id;
                            if (!Payment::where('transaction_id', $transactionId)->exists()) {
                                Payment::create([
                                    'booking_id' => $booking->id,
                                    'transaction_id' => $transactionId,
                                    'amount' => $paymentIntent->amount_received / 100,
                                    'currency' => strtoupper($paymentIntent->currency),
                                    'payment_method' => 'Stripe PaymentIntent',
                                    'status' => 'succeeded',
                                    'payment_payload' => $paymentIntent->toArray(),
                                ]);
                                
                                $booking->update([
                                    'payment_status' => 'paid',
                                ]);
                            }
                        }
                    }
                }
                break;
        }

        return response()->json(['status' => 'success']);
    }
}
/*  */