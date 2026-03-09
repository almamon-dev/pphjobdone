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
        // 4. Storing in Database
        $user = \Illuminate\Support\Facades\Auth::user();
        $isSubscribed = true; // Temporary or removing restriction

        // 5. Store in Database
        $storedAudit = \App\Models\SeoAudit::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'email' => \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->email : $request->email,
            'url' => $baseUrl,
            'response_data' => $audit,
        ]);

        $audit['audit_id'] = $storedAudit->id;
        $audit['is_subscribed'] = $isSubscribed;

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

        $overallScore = round(($techPoints + $contentScore) / 2);

        $recommendations = [];
        if (empty($siteData['description'])) {
            $recommendations[] = ['type' => 'warning', 'message' => 'Add a meta description to improve search results visibility.'];
        }
        if (empty($siteData['title'])) {
            $recommendations[] = ['type' => 'critical', 'message' => 'Missing Page Title - this is a high-priority SEO fix.'];
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
            'content_score' => round($contentScore),
            'summary' => "The website has an overall SEO score of {$overallScore}%.Technical SEO and content reach scores of {$techPoints}% and ".round($contentScore).'% respectively.',
            'recommendations' => array_slice($recommendations, 0, 4),
            'section_analysis' => [
                ['section' => 'Meta Data', 'status' => ! empty($siteData['title']) ? 'Good' : 'Critical', 'analysis' => 'Page titles and descriptions presence audit.'],
                ['section' => 'Content Audit', 'status' => $contentScore > 60 ? 'Good' : 'Needs Update', 'analysis' => 'Word count and semantic structure evaluation.'],
            ],
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
