<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class UserTasksApiController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $bookings = Booking::with('service', 'tasks')
            ->where('user_id', $userId)
            ->where('status', 'ongoing')
            ->where('payment_status', 'paid')
            ->get();

        $tasks = $bookings->map(function ($booking) {
            $items = $booking->tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'progress' => $task->progress,
                ];
            });

            $overallProgress = $booking->tasks->count() > 0 
                ? $booking->tasks->avg('progress') 
                : 0;

            return [
                'booking_id' => $booking->id,
                'plan_name' => $booking->plan_name,
                'service_title' => $booking->service?->title ?? ($booking->plan_name . ' Service'),
                'overall_progress' => (float) round($overallProgress, 2),
                'status' => ucfirst($booking->status),
                'payment_status' => ucfirst($booking->payment_status),
                'items' => $items,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => 'User progress and tasks fetched successfully',
        ]);
    }
}
