<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;

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
                'service_title' => $booking->service->title ?? ($booking->plan_name ?: 'Marketing Service'),
                'items' => [
                    [
                        'title' => 'Project Kickoff & Onboarding',
                        'description' => 'Initial discovery meeting and setup for '.($booking->service->title ?? 'service'),
                        'progress' => 100,
                        'status' => 'Completed',
                        'date' => $booking->created_at->format('M d'),
                    ],
                    [
                        'title' => 'Technical Infrastructure Setup',
                        'description' => 'Configuring accounts, tools, and tracking for '.($booking->service->title ?? 'the campaign'),
                        'progress' => 85,
                        'status' => 'In Progress',
                        'date' => $booking->created_at->addDays(2)->format('M d'),
                    ],
                    [
                        'title' => 'Strategy & Implementation Planning',
                        'description' => 'Comprehensive roadmap for the next 4 weeks of '.($booking->service->title ?? 'service'),
                        'progress' => 45,
                        'status' => 'In Progress',
                        'date' => $booking->created_at->addDays(5)->format('M d'),
                    ],
                    [
                        'title' => 'First Performance Analysis Report',
                        'description' => 'Detailed report on initial results and KPIs.',
                        'progress' => 0,
                        'status' => 'Planned',
                        'date' => $booking->created_at->addDays(14)->format('M d'),
                    ],
                    [
                        'title' => 'Monthly Optimization Review',
                        'description' => 'Refining tags, content, and strategy based on data.',
                        'progress' => 0,
                        'status' => 'Planned',
                        'date' => $booking->created_at->addDays(30)->format('M d'),
                    ],
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => 'User progress and tasks fetched successfully',
        ]);
    }
}
