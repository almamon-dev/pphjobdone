<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentInvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentApiController extends Controller
{
    use ApiResponse;

    /**
     * Return Stripe Publishable Key for dynamic frontend initialization
     */
    public function getStripeKey()
    {
        $key = config('services.stripe.key') 
            ?? env('STRIPE_KEY') 
            ?? env('STRIPE_PUBLISHABLE_KEY');

        return response()->json([
            'success' => true,
            'publishable_key' => $key,
            'stripe_key' => $key,
        ]);
    }

    /**
     * Download Custom Application Invoice PDF (Generated from Application, not Stripe)
     */
    public function downloadInvoice(Request $request, $bookingId)
    {
        $userId = Auth::id();

        if (!$userId && $request->has('token')) {
            $token = \Laravel\Sanctum\PersonalAccessToken::findToken($request->query('token'));
            if ($token) {
                $userId = $token->tokenable_id;
            }
        }

        $booking = Booking::with(['service', 'pricingPlan', 'campaignTier.campaign', 'user'])
            ->when($userId, function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->findOrFail($bookingId);

        $pdf = Pdf::loadView('pdf.invoice', compact('booking'));

        return $pdf->download('Invoice-INV-BKG-' . $booking->id . '.pdf');
    }

    /**
     * Get all payments for the authenticated user (Transactions List)
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // 1. Get recorded payments
        $payments = Payment::with(['booking.pricingPlan', 'booking.campaignTier.campaign', 'booking.service'])
            ->where(function ($q) use ($userId) {
                $q->whereHas('booking', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
                if (\Schema::hasColumn('payments', 'user_id')) {
                    $q->orWhere('user_id', $userId);
                }
            })
            ->latest()
            ->get();

        // 2. Retrieve all user bookings to include any pending/unpaid transactions as well
        $allBookings = Booking::with(['service', 'pricingPlan', 'campaignTier.campaign', 'payment'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $existingBookingIds = $payments->pluck('booking_id')->filter()->toArray();

        $additionalTransactions = $allBookings->filter(function ($b) use ($existingBookingIds) {
            return !in_array($b->id, $existingBookingIds);
        })->map(function ($b) {
            return [
                'id' => $b->id,
                'booking_id' => $b->id,
                'transaction_id' => 'TXN-' . str_pad($b->id, 6, '0', STR_PAD_LEFT),
                'amount' => (float) ($b->price ?? 0),
                'currency' => 'USD',
                'payment_method' => 'Stripe Card',
                'status' => $b->payment_status ?? 'pending',
                'created_at' => $b->created_at ? $b->created_at->toIso8601String() : now()->toIso8601String(),
                'booking' => $b,
            ];
        });

        $combined = $payments->concat($additionalTransactions)->sortByDesc('created_at')->values();

        return $this->successResponse($combined, 'Payments list fetched successfully');
    }

    /**
     * Stripe Webhook Handler
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret') ?? env('STRIPE_WEBHOOK_SECRET');

        try {
            if ($endpointSecret) {
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            } else {
                $event = \Stripe\Event::constructFrom(json_decode($payload, true));
            }
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload in Stripe Webhook: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature in Stripe Webhook: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                $subscriptionId = $invoice->subscription;
                
                $booking = Booking::with('user')->where('stripe_subscription_id', $subscriptionId)->first();
                if ($booking) {
                    $transactionId = $invoice->payment_intent ?? $invoice->id;
                    
                    if (!Payment::where('transaction_id', $transactionId)->exists()) {
                        Payment::create([
                            'booking_id' => $booking->id,
                            'transaction_id' => $transactionId,
                            'amount' => $invoice->amount_paid / 100,
                            'currency' => strtoupper($invoice->currency),
                            'payment_method' => 'Stripe Card',
                            'status' => 'succeeded',
                            'payment_payload' => $invoice->toArray(),
                        ]);
                        
                        $booking->update([
                            'payment_status' => 'paid',
                            'status' => 'active',
                        ]);
                        
                        // Send Application PDF Invoice via Mail
                        try {
                            if ($booking->user && $booking->user->email) {
                                Mail::to($booking->user->email)->send(new PaymentInvoiceMail($booking));
                                Log::info('Sent application invoice email for booking ID: ' . $booking->id);
                            }
                        } catch (\Exception $e) {
                            Log::error('Failed sending invoice email: ' . $e->getMessage());
                        }

                        Log::info('Successfully processed Stripe payment for Booking ID: ' . $booking->id);
                    }
                }
                break;
                
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                
                // Exclude invoice-generated payment intents (they are handled by invoice.payment_succeeded)
                if (empty($paymentIntent->invoice)) {
                    $bookingId = $paymentIntent->metadata->booking_id ?? null;
                    
                    if ($bookingId) {
                        $booking = Booking::with('user')->find($bookingId);
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
                                    'status' => 'active',
                                ]);

                                // Send Application PDF Invoice via Mail
                                try {
                                    if ($booking->user && $booking->user->email) {
                                        Mail::to($booking->user->email)->send(new PaymentInvoiceMail($booking));
                                        Log::info('Sent application invoice email for booking ID: ' . $booking->id);
                                    }
                                } catch (\Exception $e) {
                                    Log::error('Failed sending invoice email: ' . $e->getMessage());
                                }
                                
                                Log::info('Successfully processed Payment Intent for Booking ID: ' . $bookingId);
                            } else {
                                Log::info('Payment already exists for Transaction ID: ' . $transactionId);
                            }
                        } else {
                            Log::warning('Booking not found for ID: ' . $bookingId);
                        }
                    } else {
                        Log::warning('No booking_id found in payment intent metadata');
                    }
                } else {
                    Log::info('Payment intent belongs to an invoice (skipped, will be handled by invoice.payment_succeeded).');
                }
                break;
                
            default:
                Log::info('Unhandled webhook event type: ' . $event->type);
                break;
        }

        return response()->json(['status' => 'success']);
    }
}