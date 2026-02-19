<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;

class UserServicesApiController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $services = \App\Models\Booking::with('service')
            ->where('user_id', $userId)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'title' => $booking->service->title ?? 'Unknown Service',
                    'status' => ucfirst($booking->status),
                    'startDate' => $booking->created_at->format('F d, Y'),
                    'description' => $booking->service->subtitle ?? 'Comprehensive service management and reporting',
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
