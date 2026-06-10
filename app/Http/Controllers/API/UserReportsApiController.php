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
                    'download_link' => '#',
                    'view_link' => '#',
                ];
            });

        $reports = $seoReports->merge($bookingReports)->sortByDesc('created_at')->values();

        return response()->json([
            'success' => true,
            'data' => $reports,
            'message' => 'User reports fetched successfully',
        ]);
    }
}
