<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Services\PageSpeedService;
use App\Services\WebsiteService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SeoAuditController extends Controller
{
    use ApiResponse;

    protected WebsiteService $websiteService;

    protected PageSpeedService $pageSpeedService;

    public function __construct(
        WebsiteService $websiteService,
        PageSpeedService $pageSpeedService
    ) {
        $this->websiteService = $websiteService;
        $this->pageSpeedService = $pageSpeedService;
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

        // 2. Discover Internal Links (Max 6 pages for a complete view)
        $internalLinks = $this->websiteService->getInternalLinks($baseUrl, $siteData['html']);

        // Ensure home page is the first one
        if (! in_array($baseUrl, $internalLinks)) {
            array_unshift($internalLinks, $baseUrl);
        }
        $pagesToAudit = array_slice($internalLinks, 0, 6);

        // 3. Loop and get FULL PAGE screenshots/data
        $screenshots = [];
        $auditResults = [];

        foreach ($pagesToAudit as $index => $pageUrl) {
            // Full Page Screenshot using pageshot.site with render_delay to wait for dynamic content
            $fullPageImgUrl = 'https://pageshot.site/v1/screenshot?url='.urlencode($pageUrl).'&full_page=true&render_delay=3000';

            // Generate filename
            $prefix = $index === 0 ? 'home_full' : 'page_'.$index.'_full';
            $fileName = $prefix.'_'.time().'.jpg';
            $uploadPath = public_path('uploads/audits/'.$fileName);

            // Try to download and save the full page screenshot via Guzzle/Http for better handling
            try {
                /** @var \Illuminate\Http\Client\Response $response */
                $response = Http::timeout(60)->get($fullPageImgUrl);

                if ($response->successful()) {
                    if (! file_exists(public_path('uploads/audits'))) {
                        mkdir(public_path('uploads/audits'), 0755, true);
                    }
                    file_put_contents($uploadPath, $response->body());
                    $screenshots[] = 'uploads/audits/'.$fileName;
                }
            } catch (\Exception $e) {
                Log::error("Pageshot failed for $pageUrl: ".$e->getMessage());
            }

            // Get PageSpeed metrics (without image, to keep it fast)
            $speedData = $this->pageSpeedService->analyze($pageUrl, 'mobile');
            unset($speedData['screenshot']); // We use our full page shot instead

            $auditResults[] = [
                'url' => $pageUrl,
                'metrics' => $speedData,
            ];
        }

        // 4. Generate Main Manual SEO Analysis
        $mainSpeedData = $auditResults[0]['metrics'] ?? [];
        $audit = $this->generateManualAudit($siteData, $mainSpeedData);

        // Add multi-page data and full-page screenshots
        $audit['screenshots_paths'] = $screenshots;
        $audit['screenshots'] = Helper::generateURLArray($screenshots);
        $audit['multi_page_results'] = $auditResults;
        $audit['found_pages_count'] = count($internalLinks);

        // 5. Store in Database
        $storedAudit = \App\Models\SeoAudit::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'email' => \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->email : $request->email,
            'url' => $request->url,
            'response_data' => $audit,
        ]);

        $audit['audit_id'] = $storedAudit->id;

        return $this->sendResponse($audit, 'Full-page Multi-page SEO Audit completed.');
    }

    /**
     * Generate SEO audit data using logical rules instead of AI
     */
    protected function generateManualAudit(array $siteData, array $speedData): array
    {
        $perfScore = $speedData['scores']['performance'] ?? 0;
        $accScore = $speedData['scores']['accessibility'] ?? 0;
        $seoScore = $speedData['scores']['seo'] ?? 0;

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

        $overallScore = round(($perfScore + $seoScore + $techPoints + $contentScore) / 4);

        $recommendations = [];
        if ($perfScore < 70) {
            $recommendations[] = ['type' => 'critical', 'message' => 'Improve page load speed and Core Web Vitals.'];
        }
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
            'performance_score' => $perfScore,
            'technical_seo_score' => $techPoints,
            'content_score' => round($contentScore),
            'summary' => "The website has an overall SEO score of {$overallScore}%. Performance is rated at {$perfScore}% while technical SEO and content reach scores of {$techPoints}% and ".round($contentScore).'% respectively.',
            'recommendations' => array_slice($recommendations, 0, 4),
            'section_analysis' => [
                ['section' => 'Performance', 'status' => $perfScore > 80 ? 'Good' : 'Needs Update', 'analysis' => "Server response and page processing score: {$perfScore}%."],
                ['section' => 'Meta Data', 'status' => ! empty($siteData['title']) ? 'Good' : 'Critical', 'analysis' => 'Page titles and descriptions presence audit.'],
                ['section' => 'Accessibility', 'status' => $accScore > 80 ? 'Good' : 'Needs Update', 'analysis' => "User experience and accessibility standards: {$accScore}%."],
                ['section' => 'Content Audit', 'status' => $contentScore > 60 ? 'Good' : 'Needs Update', 'analysis' => 'Word count and semantic structure evaluation.'],
            ],
            'pagespeed' => $speedData,
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
        $auditData = $storedAudit->response_data;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.seo-audit', ['data' => $auditData]);

        return $pdf->download('SEO-Audit-'.now()->format('Y-m-d').'.pdf');
    }
}
