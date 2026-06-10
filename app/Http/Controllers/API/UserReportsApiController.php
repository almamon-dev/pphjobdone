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

        return response()->json([
            'success' => true,
            'data' => $reports,
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
}
