<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBookingApiController extends Controller
{
    use ApiResponse;

    /**
     * Get all bookings/campaigns for the authenticated user
     */
    public function index(Request $request)
    {
        $query = Booking::with(['service', 'pricingPlan', 'campaignTier.campaign', 'payments'])
            ->where('user_id', Auth::id())
            ->latest();

        if ($request->has('is_campaign')) {
            $query->where('is_campaign', filter_var($request->is_campaign, FILTER_VALIDATE_BOOLEAN));
        }

        return $this->sendResponse($query->get(), 'Bookings retrieved successfully.');
    }

    /**
     * Get specific booking details
     */
    public function show($id)
    {
        $userId = Auth::id();
        $booking = Booking::with(['service', 'pricingPlan', 'campaignTier.campaign', 'payments', 'tasks'])
            ->when($userId, function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->find($id);

        if (!$booking) {
            $booking = Booking::with(['service', 'pricingPlan', 'campaignTier.campaign', 'payments', 'tasks'])->find($id);
        }

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking details not found.'
            ], 404);
        }

        return $this->sendResponse($booking, 'Booking details retrieved.');
    }
}
