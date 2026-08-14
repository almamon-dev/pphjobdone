<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Traits\ApiResponse;

class UserServicesApiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $userId = auth()->id();

        $services = Booking::with(['service', 'pricingPlan', 'campaignTier.campaign', 'tasks'])
            ->where('user_id', $userId)
            ->latest()
            ->get()
            ->filter(function ($booking) {
                return $booking->service !== null 
                    || $booking->pricingPlan !== null 
                    || $booking->campaignTier !== null 
                    || $booking->plan_name !== null;
            })
            ->map(function ($booking) {
                $title = $booking->service?->title 
                    ?? $booking->campaignTier?->campaign?->title 
                    ?? $booking->pricingPlan?->name 
                    ?? $booking->plan_name 
                    ?? 'Marketing Order';

                $description = $booking->service?->subtitle 
                    ?? $booking->pricingPlan?->subtitle 
                    ?? $booking->pricingPlan?->description 
                    ?? 'Full optimization deliverable and milestone tracking';

                $month = $booking->created_at ? $booking->created_at->format('F Y') : now()->format('F Y');
                $avgProgress = ($booking->tasks && $booking->tasks->count() > 0) ? round($booking->tasks->avg('progress')) : 0;

                return [
                    'id' => $booking->id,
                    'title' => $title,
                    'status' => ucfirst($booking->status ?? 'Active'),
                    'startDate' => $booking->created_at ? $booking->created_at->format('M d, Y') : now()->format('M d, Y'),
                    'month' => $month,
                    'price' => '$' . number_format((float) ($booking->price ?? 0), 2),
                    'billing_cycle' => $booking->is_campaign ? 'Campaign Order' : ($booking->pricingPlan?->billing_period ?? 'One-Time Payment'),
                    'progress' => $avgProgress,
                    'description' => $description,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $services,
            'message' => 'User services fetched successfully',
        ]);
    }
}
