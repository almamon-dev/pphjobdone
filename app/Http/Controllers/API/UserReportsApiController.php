<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SeoAudit;
use Illuminate\Http\Request;

class UserReportsApiController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;
        $userEmail = $user->email;

        $seoReports = SeoAudit::where('user_id', $userId)
            ->orWhereRaw('LOWER(email) = ?', [strtolower($userEmail)])
            ->latest()
            ->get()
            ->map(function ($audit) {
                return [
                    'id' => 'seo_' . $audit->id,
                    'title' => ($audit->url ? parse_url($audit->url, PHP_URL_HOST) : 'SEO') . ' Analysis',
                    'type' => 'SEO Audit',
                    'status' => 'Available',
                    'date' => $audit->created_at->format('Y-m-d'),
                    'created_at' => $audit->created_at->toISOString(),
                    'download_link' => url("/api/seo-audit/download?audit_id=" . $audit->id),
                    'view_link' => '#',
                    'data' => $audit->response_data,
                ];
            });

        $bookingReports = \App\Models\Booking::with('service')
            ->where('user_id', $userId)
            ->whereIn('status', ['ongoing', 'active'])
            ->where('payment_status', 'paid')
            ->latest()
            ->get()
            ->map(function ($booking) {
                $serviceTitle = $booking->service?->title ?? ($booking->plan_name . ' Service');
                return [
                    'id' => 'bkg_' . $booking->id,
                    'title' => $serviceTitle . ' - Progress Report',
                    'type' => 'Campaign Report',
                    'status' => 'Available',
                    'date' => now()->format('Y-m-d'),
                    'created_at' => now()->toISOString(),
                    'download_link' => url("/api/campaign-report/download?booking_id=" . $booking->id),
                    'view_link' => '/dashboard/progress-tasks',
                ];
            });

        $reports = $seoReports->merge($bookingReports)->sortByDesc('created_at')->values();

        // Dynamically calculate Average Growth (active booking task progress & SEO Audit Scores)
        $totalProgress = 0;
        $taskCount = 0;
        $activeBookingsForStats = \App\Models\Booking::where('user_id', $userId)
            ->whereIn('status', ['ongoing', 'active'])
            ->with('tasks')
            ->get();
            
        foreach ($activeBookingsForStats as $booking) {
            $bookingTasks = $booking->tasks;
            if ($bookingTasks->count() > 0) {
                $totalProgress += $bookingTasks->avg('progress');
                $taskCount++;
            }
        }

        // Fallback to average score of user's SEO audits if no active tasks
        if ($taskCount === 0) {
            $userAudits = SeoAudit::where('user_id', $userId)
                ->orWhereRaw('LOWER(email) = ?', [strtolower($userEmail)])
                ->get();

            $auditScores = [];
            foreach ($userAudits as $audit) {
                $respData = is_array($audit->response_data) ? $audit->response_data : json_decode($audit->response_data, true);
                $score = $respData['overall_score'] ?? $respData['score'] ?? null;
                if (is_numeric($score) && $score > 0) {
                    $auditScores[] = (float)$score;
                }
            }

            if (count($auditScores) > 0) {
                $totalProgress = array_sum($auditScores);
                $taskCount = count($auditScores);
            }
        }

        $avgGrowth = $taskCount > 0 ? round($totalProgress / $taskCount) : 0;

        return response()->json([
            'success' => true,
            'data' => $reports,
            'avg_growth' => $avgGrowth,
            'message' => 'User reports fetched successfully',
        ]);
    }

    public function downloadCampaignReport(Request $request)
    {
        $bookingId = $request->query('booking_id');
        if (!$bookingId) {
            return response()->json(['error' => 'Booking ID is required'], 400);
        }

        $booking = \App\Models\Booking::with(['service', 'tasks', 'user'])->find($bookingId);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.campaign-report', ['booking' => $booking]);
        return $pdf->download('Campaign-Progress-Report-BKG-' . $booking->id . '.pdf');
    }

    public function getAiReportSummary(Request $request, \App\Services\OpenAiService $openAiService)
    {
        $bookingId = $request->query('booking_id');
        if (!$bookingId) {
            return response()->json(['error' => 'Booking ID is required'], 400);
        }

        $booking = \App\Models\Booking::with(['service', 'tasks', 'user'])->find($bookingId);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        $completedTasks = $booking->tasks->filter(fn($t) => $t->progress >= 100)->pluck('title')->toArray();
        $inProgressTasks = $booking->tasks->filter(fn($t) => $t->progress > 0 && $t->progress < 100)->pluck('title')->toArray();
        $overallProgress = $booking->tasks->count() > 0 ? round($booking->tasks->avg('progress')) : 0;

        $prompt = "Generate a concise executive summary for an SEO marketing campaign. " .
                  "Service: {$booking->service?->title}. Overall Progress: {$overallProgress}%. " .
                  "Completed Milestones: " . implode(', ', $completedTasks) . ". " .
                  "In Progress: " . implode(', ', $inProgressTasks) . ". " .
                  "Return JSON format with keys: 'executive_summary', 'key_achievements' (array), 'suggested_actions' (array).";

        try {
            $aiResponse = $openAiService->askAi($prompt);
            $parsedData = json_decode($aiResponse, true);
            if (!is_array($parsedData) || !isset($parsedData['executive_summary'])) {
                $parsedData = [
                    'executive_summary' => "Your campaign for {$booking->service?->title} is progressing smoothly with an overall completion rate of {$overallProgress}%.",
                    'key_achievements' => count($completedTasks) > 0 ? $completedTasks : ['Campaign setup & initial audit completed'],
                    'suggested_actions' => ['Review monthly progress report', 'Schedule strategy sync call']
                ];
            }
        } catch (\Exception $e) {
            $parsedData = [
                'executive_summary' => "Your campaign for {$booking->service?->title} is currently active and on track.",
                'key_achievements' => ['Service initialized and task pipeline generated'],
                'suggested_actions' => ['Track progress in dashboard']
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $parsedData
        ]);
    }
}
