<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\OpenAiService;
use App\Services\WebsiteService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SeoAuditController extends Controller
{
    use ApiResponse;

    protected OpenAiService $openAiService;

    protected WebsiteService $websiteService;

    public function __construct(OpenAiService $openAiService, WebsiteService $websiteService)
    {
        $this->openAiService = $openAiService;
        $this->websiteService = $websiteService;
    }

    /**
     * Handle website SEO audit request
     * Takes a URL, fetches content, and generates AI SEO overview
     */
    public function audit(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        // 1. Fetch website content
        $siteData = $this->websiteService->fetchContent($request->url);

        if (isset($siteData['error'])) {
            return $this->sendError($siteData['error'], [], 400);
        }

        // 2. Generate AI SEO Analysis
        $audit = $this->openAiService->generateAudit($siteData);

        if ($audit) {
            // 3. Store in Database
            $storedAudit = \App\Models\SeoAudit::create([
                'user_id' => auth()->id(),
                'url' => $request->url,
                'response_data' => $audit,
            ]);

            // Add ID to response so frontend can use it for download
            $audit['audit_id'] = $storedAudit->id;

            return $this->sendResponse($audit, 'SEO Audit generated successfully.');
        }

        return $this->sendError('Failed to generate SEO audit.', [], 500);
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
