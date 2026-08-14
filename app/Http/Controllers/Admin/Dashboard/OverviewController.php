<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\Service;
use App\Models\PricingPlan;
use App\Models\Campaign;
use App\Models\ContactMessage;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class OverviewController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;
        $period = $request->input('period', '7_days');

        if ($user->is_admin) {
            // 1. Total counts from database
            $usersCount = User::count();
            $servicesCount = Service::count();
            $bookingsCount = Booking::count();
            $campaignsCount = Campaign::count();
            $plansCount = PricingPlan::count();
            $contactsCount = ContactMessage::count();

            // 2. Revenue calculations from DB
            $paymentsSum = (float) Payment::whereIn('status', ['succeeded', 'paid', 'completed'])->sum('amount');
            $bookingsSum = (float) Booking::whereIn('payment_status', ['paid', 'succeeded', 'completed'])->sum('price');
            $totalRevenue = max($paymentsSum, $bookingsSum);

            // 3. Dynamic Growth & Trends
            $thisMonthBookings = Booking::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            $lastMonthBookings = Booking::whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count();
            $bookingsGrowth = $lastMonthBookings > 0
                ? round((($thisMonthBookings - $lastMonthBookings) / $lastMonthBookings) * 100, 1)
                : ($thisMonthBookings > 0 ? 100 : 0);

            $thisMonthRevenue = Booking::whereIn('payment_status', ['paid', 'succeeded', 'completed'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('price');
            $lastMonthRevenue = Booking::whereIn('payment_status', ['paid', 'succeeded', 'completed'])
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->sum('price');
            $revenueGrowth = $lastMonthRevenue > 0
                ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
                : ($thisMonthRevenue > 0 ? 100 : 0);

            $completedBookings = Booking::where('status', 'completed')->count();
            $successRate = $bookingsCount > 0 ? round(($completedBookings / $bookingsCount) * 100, 1) : 100;

            // 4. Dynamic Filtered Line Chart Data
            $lineChartData = [];
            if ($period === '30_days') {
                for ($i = 27; $i >= 0; $i -= 3) {
                    $day = now()->subDays($i);
                    $count = Booking::whereDate('created_at', '>=', $day->copy()->subDays(2)->toDateString())
                        ->whereDate('created_at', '<=', $day->toDateString())
                        ->count();
                    $lineChartData[] = [
                        'label' => $day->format('M d'),
                        'val' => $count,
                    ];
                }
            } elseif ($period === '12_months') {
                for ($i = 11; $i >= 0; $i--) {
                    $month = now()->subMonths($i);
                    $count = Booking::whereMonth('created_at', $month->month)
                        ->whereYear('created_at', $month->year)
                        ->count();
                    $lineChartData[] = [
                        'label' => $month->format('M'),
                        'val' => $count,
                    ];
                }
            } else {
                // Default 7_days
                for ($i = 6; $i >= 0; $i--) {
                    $day = now()->subDays($i);
                    $count = Booking::whereDate('created_at', $day->toDateString())->count();
                    $lineChartData[] = [
                        'label' => $day->format('D'),
                        'val' => $count,
                    ];
                }
            }

            // 5. Bar Chart Data (Last 5 Weeks Booking Activity from DB)
            $barChartData = [];
            for ($w = 4; $w >= 0; $w--) {
                $startOfWeek = now()->subWeeks($w)->startOfWeek();
                $endOfWeek = now()->subWeeks($w)->endOfWeek();
                $count = Booking::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
                $barChartData[] = [
                    'label' => 'Week ' . (5 - $w),
                    'val' => $count,
                ];
            }

            // 6. Recent tables data
            $recentBookings = Booking::with(['user', 'service'])
                ->latest()
                ->take(5)
                ->get();

            $recentContacts = ContactMessage::latest()
                ->take(5)
                ->get();

            $recentUsers = User::latest()
                ->take(5)
                ->get();

            return Inertia::render('Admin/Dashboard', [
                'stats' => [
                    'users' => $usersCount,
                    'services' => $servicesCount,
                    'bookings' => $bookingsCount,
                    'revenue' => (float) $totalRevenue,
                    'campaigns' => $campaignsCount,
                    'plans' => $plansCount,
                    'contacts' => $contactsCount,
                    'bookingsGrowth' => $bookingsGrowth,
                    'revenueGrowth' => $revenueGrowth,
                    'successRate' => $successRate,
                ],
                'filters' => [
                    'period' => $period,
                ],
                'lineChartData' => $lineChartData,
                'barChartData' => $barChartData,
                'recentBookings' => $recentBookings,
                'recentContacts' => $recentContacts,
                'recentUsers' => $recentUsers,
            ]);
        }

        // --- Logic for Client / User Dashboard ---
        $activeBookings = Booking::where('user_id', $userId)
            ->where('status', 'ongoing')
            ->where('payment_status', 'paid')
            ->get();
        
        $activeServicesCount = $activeBookings->count();

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

        $supportMessagesCount = \App\Models\Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();

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

        $paymentActivities = Payment::whereHas('booking', function ($q) use ($userId) {
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
