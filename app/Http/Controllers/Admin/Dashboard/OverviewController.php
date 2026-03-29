<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class OverviewController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;

        // Counts
        $usersCount = User::count();
        $servicesCount = \App\Models\Service::count();

        if ($user->is_admin) {
            return Inertia::render('Admin/Dashboard', [
                'stats' => [
                    'users' => $usersCount,
                    'services' => $servicesCount,
                    'bookings' => \App\Models\Booking::count(),
                ]
            ]);
        }

        // --- Logic for User Dashboard ---
        
        // 1. Stats Calculation
        $activeBookings = \App\Models\Booking::where('user_id', $userId)
            ->where('status', 'ongoing')
            ->where('payment_status', 'paid')
            ->get();
        
        $activeServicesCount = $activeBookings->count();

        // Dynamic Progress Calculation
        $totalProgress = 0;
        $taskCount = 0;
        foreach ($activeBookings as $booking) {
            $bookingTasks = $booking->tasks;
            if ($bookingTasks->count() > 0) {
                $totalProgress += $bookingTasks->avg('progress');
                $taskCount++;
            }
        }
        $avgProgress = $taskCount > 0 ? round($totalProgress / $taskCount) : 0;

        $latestAudit = \App\Models\SeoAudit::where('user_id', $userId)->latest()->first();
        $reportStatus = $latestAudit ? 'Available' : 'Pending';
        $reportSubtitle = $latestAudit ? $latestAudit->created_at->format('F Y') : 'No Reports';

        // Support Messages (Dynamic)
        $supportMessagesCount = \App\Models\Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();

        // 2. Activities (Combined from Audits and Payments)
        $auditActivities = \App\Models\SeoAudit::where('user_id', $userId)
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($audit) {
                return [
                    'title' => 'SEO Audit Completed',
                    'description' => "Audit for {$audit->url} is ready.",
                    'time' => $audit->created_at->diffForHumans(),
                    'color' => 'bg-emerald-500',
                ];
            });

        $paymentActivities = \App\Models\Payment::whereHas('booking', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($payment) {
                return [
                    'title' => 'Payment Successful',
                    'description' => "Payment of {$payment->currency} {$payment->amount} received.",
                    'time' => $payment->created_at->diffForHumans(),
                    'color' => 'bg-purple-500',
                ];
            });

        $activities = $auditActivities->concat($paymentActivities)->sortByDesc('time')->values()->take(5);

        if ($activities->isEmpty()) {
            $activities = collect([[
                'title' => 'Welcome to PPHJobDone',
                'description' => 'Start by exploring our services or creating an audit.',
                'time' => 'Just now',
                'color' => 'bg-blue-500',
            ]]);
        }

        return Inertia::render('Dashboard', [
            'dashboard_data' => [
                'stats' => [
                    [
                        'title' => 'Active Services',
                        'value' => (string) $activeServicesCount,
                        'subtitle' => $activeServicesCount > 0 ? 'All Running Smoothly' : 'No Active Services',
                        'icon' => 'Briefcase',
                        'color' => 'bg-purple-600',
                    ],
                    [
                        'title' => 'Current Month Progress',
                        'value' => (string) $avgProgress . '%', 
                        'subtitle' => $avgProgress >= 80 ? 'On Track' : ($avgProgress > 0 ? 'In Progress' : 'Not Started'),
                        'icon' => 'BarChart',
                        'color' => 'bg-indigo-600',
                    ],
                    [
                        'title' => 'Latest Report Status',
                        'value' => $reportStatus,
                        'subtitle' => $reportSubtitle,
                        'icon' => 'FileText',
                        'color' => 'bg-blue-600',
                    ],
                    [
                        'title' => 'Support Messages',
                        'value' => (string) $supportMessagesCount,
                        'subtitle' => $supportMessagesCount > 0 ? "You have $supportMessagesCount new messages" : 'No New Messages',
                        'icon' => 'MessageSquare',
                        'color' => 'bg-pink-600',
                    ],
                ],
                'activities' => $activities,
            ]
        ]);
    }
}
