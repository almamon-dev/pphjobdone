<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\Stripe;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('user', 'service', 'pricingPlan')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => $bookings
        ]);
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'service', 'pricingPlan', 'tasks' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }]);

        return Inertia::render('Admin/Bookings/Show', [
            'booking' => $booking
        ]);
    }

    public function toggleAutoRenew(Booking $booking)
    {
        $shouldCancel = !$booking->cancel_at_period_end;
        $stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET');

        if ($booking->stripe_subscription_id && $stripeSecret) {
            try {
                Stripe::setApiKey($stripeSecret);
                $sub = \Stripe\Subscription::update($booking->stripe_subscription_id, [
                    'cancel_at_period_end' => $shouldCancel,
                ]);
                $booking->update([
                    'cancel_at_period_end' => (bool) $sub->cancel_at_period_end,
                    'stripe_status' => $sub->status,
                    'current_period_end' => isset($sub->current_period_end) ? date('Y-m-d H:i:s', $sub->current_period_end) : $booking->current_period_end,
                ]);
            } catch (\Exception $e) {
                return back()->with('error', 'Stripe update error: ' . $e->getMessage());
            }
        } else {
            $booking->update(['cancel_at_period_end' => $shouldCancel]);
        }

        $msg = $shouldCancel ? 'Auto-renewal disabled for this booking.' : 'Auto-renewal enabled for this booking.';
        return back()->with('success', $msg);
    }

    public function storeTask(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'progress' => 'required|numeric|min:0|max:100',
            'status' => 'required|string|in:pending,ongoing,completed',
            'due_date' => 'nullable|date',
        ]);

        $booking->tasks()->create($validated);

        return back()->with('success', 'Task created successfully.');
    }

    public function updateTask(Request $request, Booking $booking, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'progress' => 'required|numeric|min:0|max:100',
            'status' => 'required|string|in:pending,ongoing,completed',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return back()->with('success', 'Task updated successfully.');
    }

    public function destroyTask(Booking $booking, Task $task)
    {
        $task->delete();

        return back()->with('success', 'Task deleted successfully.');
    }
}
