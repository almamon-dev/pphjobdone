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
        $activeBookings = \App\Models\Booking::where('user_id', $userId)
            ->whereIn('status', ['ongoing', 'active', 'in_progress'])
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
        $supportMessagesCount = \App\Models\Message::where('receiver_id', $userId)->where('is_read', false)->count();

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

        // 3. Performance & Booking Analytics Chart Data (Dynamic DB)
        $months = collect([]);
        $bookingsSeries = [];
        $paymentsSeries = [];
        $tasksSeries = [];

        $totalUserBookings = \App\Models\Booking::where('user_id', $userId)->count();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->format('M');
            $year = $date->year;
            $monthNum = $date->month;

            $months->push($monthLabel);

            // Real monthly bookings created
            $monthlyBookings = \App\Models\Booking::where('user_id', $userId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $monthNum)
                ->count();

            // Real monthly payments made
            $monthlyPayments = (float) \App\Models\Payment::whereHas('booking', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $monthNum)
                ->sum('amount');

            // Real monthly tasks completed
            $monthlyTasks = \App\Models\Task::whereHas('booking', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
                ->whereYear('updated_at', $year)
                ->whereMonth('updated_at', $monthNum)
                ->where('status', 'completed')
                ->count();

            // Strict real database values
            $bookingsSeries[] = $monthlyBookings;
            $paymentsSeries[] = $monthlyPayments;
            $tasksSeries[] = $monthlyTasks;
        }

        // Dynamic KPI Summary calculations
        $totalCompletedTasks = \App\Models\Task::whereHas('booking', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'completed')->count();

        $prevMonthBookings = $bookingsSeries[count($bookingsSeries) - 2] ?? 0;
        $currMonthBookings = end($bookingsSeries) ?: 0;
        
        $growthRateStr = '0%';
        if ($prevMonthBookings > 0) {
            $growthRate = round((($currMonthBookings - $prevMonthBookings) / $prevMonthBookings) * 100, 1);
            $growthRateStr = ($growthRate >= 0 ? '+' : '') . $growthRate . '%';
        }

        $totalPayments = (float) \App\Models\Payment::whereHas('booking', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->sum('amount');

        $chartData = [
            'labels' => $months->toArray(),
            'overview' => [
                'bookings' => $bookingsSeries,
                'payments' => $paymentsSeries,
                'tasks' => $tasksSeries,
            ],
            'kpis' => [
                'growth_rate' => $growthRateStr,
                'total_bookings' => (string) $totalUserBookings,
                'total_spent' => '$' . number_format($totalPayments, 2),
                'completed_tasks' => (string) $totalCompletedTasks,
                'health_score' => $avgProgress > 0 ? $avgProgress . '%' : '0%',
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
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
                        'value' => (string) $avgProgress, 
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
                'chart' => $chartData,
                'activities' => $activities,
            ],
            'message' => 'User dashboard data fetched successfully',
        ]);
    }

    public function getAiInsights(Request $request, \App\Services\OpenAiService $openAiService)
    {
        $userId = auth()->id();

        // Gather basic data for AI
        $activeBookings = \App\Models\Booking::where('user_id', $userId)
            ->whereIn('status', ['ongoing', 'active', 'in_progress'])
            ->where('payment_status', 'paid')
            ->with(['service:id,title', 'tasks' => function ($q) {
                $q->select('id', 'booking_id', 'title', 'progress', 'status');
            }])
            ->get();

        $latestAudit = \App\Models\SeoAudit::where('user_id', $userId)->latest()->first();

        $userData = [
            'active_services_count' => $activeBookings->count(),
            'services' => $activeBookings->map(function ($b) {
                return [
                    'title' => $b->service->title ?? $b->plan_name,
                    'overall_progress' => round($b->tasks->avg('progress') ?? 0),
                    'pending_tasks' => $b->tasks->where('status', 'pending')->pluck('title')->toArray(),
                    'in_progress_tasks' => $b->tasks->where('status', 'in_progress')->pluck('title')->toArray(),
                    'completed_tasks' => $b->tasks->where('status', 'completed')->pluck('title')->toArray(),
                ];
            })->toArray(),
            'latest_seo_audit' => $latestAudit ? [
                'url' => $latestAudit->url,
                'score' => $latestAudit->response_data['overall_score'] ?? ($latestAudit->overall_score ?? null),
                'recommendations' => array_slice($latestAudit->response_data['recommendations'] ?? [], 0, 2)
            ] : null,
        ];

        // Call OpenAI
        $insights = $openAiService->generateDashboardInsights($userData);

        if (isset($insights['error'])) {
            return $this->sendError($insights['error'], [], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $insights,
            'message' => 'AI insights generated successfully',
        ]);
    }
}
