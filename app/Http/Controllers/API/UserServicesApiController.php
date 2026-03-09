<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Traits\ApiResponse;

class UserServicesApiController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $userId = auth()->id();

        $services = \App\Models\Booking::with(['service', 'pricingPlan'])
            ->where('user_id', $userId)
            ->where('payment_status', 'paid')
            ->get()
            ->filter(function ($booking) {
                return $booking->service !== null || $booking->pricingPlan !== null;
            })
            ->map(function ($booking) {
                $title = $booking->service->title ?? ($booking->pricingPlan->name ?? ($booking->plan_name ?? 'Unknown Service'));
                $description = $booking->service->subtitle ?? ($booking->pricingPlan->description ?? 'Service details');
                
                return [
                    'id' => $booking->id,
                    'title' => $title,
                    'status' => ucfirst($booking->status),
                    'startDate' => $booking->created_at->format('F d, Y'),
                    'description' => $description,
                    'pogress_link' => '#', 
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $services,
            'message' => 'User services fetched successfully',
        ]);
    }
}
