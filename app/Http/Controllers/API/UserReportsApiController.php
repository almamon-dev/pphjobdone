<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SeoAudit;
use Illuminate\Http\Request;

class UserReportsApiController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $reports = SeoAudit::where('user_id', $userId)
            ->latest()
            ->get()
            ->map(function ($audit) {
                return [
                    'id' => $audit->id,
                    'title' => ($audit->url ? parse_url($audit->url, PHP_URL_HOST) : 'SEO') . ' Report',
                    'subtitle' => 'SEO Monthly • ' . $audit->created_at->format('F d, Y'),
                    'status' => 'Available',
                    'date' => $audit->created_at->format('Y-m-d'),
                    'download_url' => url("/api/seo-audit/download"), // Placeholder if needed
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $reports,
            'message' => 'User reports fetched successfully',
        ]);
    }
}
