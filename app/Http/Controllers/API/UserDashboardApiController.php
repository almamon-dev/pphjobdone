<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class UserDashboardApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $userId = auth()->id();

        // 1. Stats Calculation
        $activeServicesCount = \App\Models\Booking::where('user_id', $userId)
            ->where('status', 'ongoing')
            ->count();

        $latestAudit = \App\Models\SeoAudit::where('user_id', $userId)->latest()->first();
        $reportStatus = $latestAudit ? 'Available' : 'Pending';
        $reportSubtitle = $latestAudit ? $latestAudit->created_at->format('F Y') : 'No Reports';

        // Support Messages (Dynamic if table exists, otherwise keep 0)
        // $supportMessagesCount = \App\Models\Message::where('user_id', $userId)->where('is_read', false)->count();

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
                'title' => 'Welcome to Gajura',
                'description' => 'Start by exploring our services or creating an audit.',
                'time' => 'Just now',
                'color' => 'bg-blue-500',
            ]]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    [
                        'title' => 'Active Services',
                        'value' => (string) $activeServicesCount,
                        'subtitle' => 'All Running Smoothly',
                        'icon' => 'Briefcase',
                        'color' => 'bg-purple-600',
                    ],
                    [
                        'title' => 'Current Month Progress',
                        'value' => '78', // Dynamic progress logic can be added later
                        'subtitle' => 'On Track',
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
                        'value' => '0',
                        'subtitle' => 'No New Messages',
                        'icon' => 'MessageSquare',
                        'color' => 'bg-pink-600',
                    ],
                ],
                'activities' => $activities,
            ],
            'message' => 'User dashboard data fetched successfully',
        ]);
    }
}
