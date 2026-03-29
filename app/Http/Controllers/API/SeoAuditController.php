<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Services\OpenAiService;
use App\Services\WebsiteService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SeoAuditController extends Controller
{
    use ApiResponse;

    protected WebsiteService $websiteService;

    protected OpenAiService $openAiService;

    public function __construct(
        WebsiteService $websiteService,
            OpenAiService $openAiService
    ) {
        $this->websiteService = $websiteService;
        $this->openAiService = $openAiService;
    }

    /**
     * Handle website SEO audit request
     * Takes a URL, fetches content, and generates SEO overview
     */
    public function audit(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'email' => 'nullable|email',
        ]);

        if (! \Illuminate\Support\Facades\Auth::check() && ! $request->email) {
            return $this->sendError('Email is required for guest audit.', [], 400);
        }

        $baseUrl = rtrim($request->url, '/');

        // 1. Fetch main website content
        $siteData = $this->websiteService->fetchContent($baseUrl);

        if (isset($siteData['error'])) {
            return $this->sendError($siteData['error'], [], 400);
        }
        
        // 2. Generate AI SEO Analysis using OpenAI
        $audit = $this->openAiService->generateSeoRecommendations($siteData);

        // Fallback to manual if AI fails
        if (isset($audit['error'])) {
            $audit = $this->generateManualAudit($siteData);
        }

        // Add metadata (No images as requested)
        $audit['screenshots_paths'] = [];
        $audit['screenshots'] = [];
        $audit['found_pages_count'] = 0; // Simplified
        $audit['multi_page_results'] = [
            [
                'url' => $baseUrl,
            ]
        ];
        // 5. Determine User ID and Subscription Status
        $userId = \Illuminate\Support\Facades\Auth::id();
        $auditEmail = $request->email;
        $isSubscribed = \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->is_subscribed : false;

        if (! $userId && $request->email) {
            $existingUser = \App\Models\User::where('email', $request->email)->first();
            if ($existingUser) {
                $userId = $existingUser->id;
                $auditEmail = $existingUser->email;
                $isSubscribed = $existingUser->is_subscribed;
            }
        } elseif ($userId) {
            $auditEmail = \Illuminate\Support\Facades\Auth::user()->email;
            $isSubscribed = \Illuminate\Support\Facades\Auth::user()->is_subscribed;
        }

        // 6. Store in Database
        $storedAudit = \App\Models\SeoAudit::create([
            'user_id' => $userId,
            'email' => $auditEmail,
            'url' => $baseUrl,
            'response_data' => $audit,
        ]);

        $audit['audit_id'] = $storedAudit->id;
        $audit['is_subscribed'] = $isSubscribed;

        // NEW: Link Audit with Booking Tasks
        if ($userId) {
            $latestBooking = \App\Models\Booking::where('user_id', $userId)
                ->where('status', 'ongoing')
                ->latest()
                ->first();
            
            if ($latestBooking) {
                $latestBooking->tasks()->create([
                    'title' => 'SEO Audit Report Generated',
                    'description' => 'Comprehensive SEO analysis generated for: ' . $baseUrl,
                    'progress' => 100,
                    'status' => 'completed',
                    'due_date' => now(),
                ]);
            }
        }

        return $this->sendResponse($audit, $isSubscribed ? 'Chat GPT powered SEO Audit completed.' : 'Audit Preview: Upgrade to see full results.');
    }

    /**
     * Generate SEO audit data using logical rules instead of AI
     */
    protected function generateManualAudit(array $siteData): array
    {
        // Calculate Technical Score based on metadata presence
        $techPoints = 0;
        if (! empty($siteData['title'])) {
            $techPoints += 30;
        }
        if (! empty($siteData['description'])) {
            $techPoints += 30;
        }
        if (! empty($siteData['headings'])) {
            $techPoints += 20;
        }
        if (($siteData['image_count'] ?? 0) > 0) {
            $techPoints += 20;
        }

        $contentScore = min(100, (str_word_count($siteData['content'] ?? '') / 500) * 100);
        if ($contentScore < 30 && ! empty($siteData['content'])) {
            $contentScore = 50;
        }

        // Performance Score logic
        $loadTime = $siteData['load_time'] ?? 1000;
        $pageSize = $siteData['page_size'] ?? 100000;
        
        $loadScore = max(0, 100 - ($loadTime / 50));
        $sizeScore = max(0, 100 - ($pageSize / 20000));
        $perfScore = round(($loadScore + $sizeScore) / 2);

        $overallScore = round(($techPoints + $contentScore + $perfScore) / 3);

        $recommendations = [];
        if (empty($siteData['description'])) {
            $recommendations[] = ['type' => 'warning', 'message' => 'Add a meta description to improve search results visibility.'];
        }
        if (empty($siteData['title'])) {
            $recommendations[] = ['type' => 'critical', 'message' => 'Missing Page Title - this is a high-priority SEO fix.'];
        }
        if ($loadTime > 2500) {
            $recommendations[] = ['type' => 'critical', 'message' => 'Page load time is too slow ('.round($loadTime/1000, 1).'s). Optimize images and scripts.'];
        }
        if (($siteData['link_count'] ?? 0) < 5) {
            $recommendations[] = ['type' => 'info', 'message' => 'Consider adding more internal and external links.'];
        }

        // Fill up to 4 recommendations if needed
        if (count($recommendations) < 4) {
            $recommendations[] = ['type' => 'info', 'message' => 'Verify mobile responsiveness for better ranking.'];
        }

        return [
            'website_url' => $siteData['url'],
            'overall_score' => $overallScore,
            'technical_seo_score' => $techPoints,
            'performance_score' => $perfScore,
            'content_score' => round($contentScore),
            'summary' => "The website has an overall SEO score of {$overallScore}%. Technical, Content and Performance scores reached {$techPoints}%, ".round($contentScore).'% and '.$perfScore.'% respectively.',
            'recommendations' => array_slice($recommendations, 0, 4),
            'section_analysis' => [
                ['section' => 'Meta Data', 'status' => ! empty($siteData['title']) ? 'Good' : 'Critical', 'analysis' => 'Page titles and descriptions presence audit.'],
                ['section' => 'Content Audit', 'status' => $contentScore > 60 ? 'Good' : 'Needs Update', 'analysis' => 'Word count and semantic structure evaluation.'],
                ['section' => 'Performance', 'status' => $perfScore > 70 ? 'Good' : ($perfScore > 40 ? 'Needs Update' : 'Critical'), 'analysis' => 'Load time and page size optimization check.'],
            ],
            'performance_metrics' => [
                'load_time' => $loadTime,
                'page_size' => $pageSize,
                'script_count' => $siteData['script_count'] ?? 0,
                'style_count' => $siteData['style_count'] ?? 0,
            ]
        ];
    }

    /**
     * Download SEO Audit as PDF
     * Now fetches from database using ID to simplify frontend request
     */
    public function downloadPdf(Request $request)
    {
        $request->validate([
            'audit_id' => 'required|exists:seo_audits,id',
        ]);

        $storedAudit = \App\Models\SeoAudit::findOrFail($request->audit_id);
        
        $user = \Illuminate\Support\Facades\Auth::user();

        $auditData = $storedAudit->response_data;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.seo-audit', ['data' => $auditData]);

        return $pdf->download('SEO-Audit-'.now()->format('Y-m-d').'.pdf');
    }
}
