<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class UserTasksApiController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $bookings = Booking::with('service')
            ->where('user_id', $userId)
            ->where('status', 'ongoing')
            ->get();

        $tasks = $bookings->map(function ($booking) {
            // Generating some mock tasks based on the service title
            return [
                'service_title' => $booking->service->title ?? 'Marketing Service',
                'items' => [
                    [
                        'title' => 'Initial Strategy Setup',
                        'description' => 'Planning and goal setting for ' . ($booking->service->title ?? 'service'),
                        'progress' => 100,
                        'status' => 'Completed',
                    ],
                    [
                        'title' => 'Performance Analysis',
                        'description' => 'Bi-weekly check on campaign performance.',
                        'progress' => 45,
                        'status' => 'In Progress',
                    ],
                    [
                        'title' => 'Monthly Optimization',
                        'description' => 'Refining tags and content based on data.',
                        'progress' => 10,
                        'status' => 'In Progress',
                    ],
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => 'User progress and tasks fetched successfully',
        ]);
    }
}
