<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiService
{
    protected string $apiKey;

    protected string $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openai.key') ?? env('OPENAI_API_KEY');
    }

    /**
     * Send a prompt to ChatGPT with retry mechanism
     */
    public function askChatGpt(string $prompt, string $model = 'gpt-3.5-turbo', int $maxRetries = 3): ?string
    {
        $attempts = 0;
        while ($attempts < $maxRetries) {
            try {
                $response = Http::timeout(30)
                    ->withHeaders([
                        'Authorization' => 'Bearer '.$this->apiKey,
                        'Content-Type' => 'application/json',
                    ])->post($this->baseUrl.'/chat/completions', [
                        'model' => $model,
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'You are an elite SEO auditor. Provide logical, data-driven, and highly professional insights.',
                            ],
                            [
                                'role' => 'user',
                                'content' => $prompt,
                            ],
                        ],
                        'temperature' => 0.4,
                    ]);

                if ($response->successful()) {
                    return $response->json('choices.0.message.content');
                }

                if ($response->status() === 429) {
                    $attempts++;
                    sleep(pow(2, $attempts));

                    continue;
                }

                Log::error('OpenAI API Error: '.$response->body());

                return null;
            } catch (\Exception $e) {
                Log::error('OpenAI Exception: '.$e->getMessage());
                $attempts++;
                if ($attempts >= $maxRetries) {
                    return null;
                }
                sleep(1);
            }
        }

        return null;
    }

    public function generateAudit(array $siteData): ?array
    {
        $cacheKey = 'seo_audit_'.md5($siteData['url']);

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($siteData) {
            return $this->performDetailedAudit($siteData);
        });
    }

    protected function performDetailedAudit(array $siteData): ?array
    {
        $metrics = $this->calculateBasicSeoMetrics($siteData);
        $prompt = $this->buildEnhancedAuditPrompt($siteData, $metrics);

        $response = $this->askChatGpt($prompt, 'gpt-3.5-turbo-0125');

        if (! $response) {
            return $this->generateFallbackAudit($siteData, $metrics);
        }

        $auditData = json_decode($this->cleanJsonResponse($response), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->generateFallbackAudit($siteData, $metrics);
        }

        return $auditData;
    }

    protected function calculateBasicSeoMetrics(array $siteData): array
    {
        return [
            'title_length' => strlen($siteData['title'] ?? ''),
            'description_length' => strlen($siteData['description'] ?? ''),
            'content_length' => str_word_count($siteData['content'] ?? ''),
            'has_title' => ! empty($siteData['title']),
            'has_description' => ! empty($siteData['description']),
            'title_optimal' => strlen($siteData['title'] ?? '') >= 30 && strlen($siteData['title'] ?? '') <= 60,
            'description_optimal' => strlen($siteData['description'] ?? '') >= 120 && strlen($siteData['description'] ?? '') <= 160,
        ];
    }

    protected function buildEnhancedAuditPrompt(array $siteData, array $metrics): string
    {
        return <<<EOT
You are an Elite Technical SEO Auditor. Analyze the following refined data:

WEBSITE DATA:
- URL: {$siteData['url']}
- Title: {$siteData['title']}
- Meta Description: {$siteData['description']}
- Headings: {$siteData['headings']}
- Images: {$siteData['image_count']} | Links: {$siteData['link_count']}
- Content Word Count: {$metrics['content_length']}

PERFORM A PROFESSIONAL AUDIT AND RETURN ONLY A JSON OBJECT:

{
    "website_url": "{$siteData['url']}",
    "overall_score": 0-100,
    "performance_score": 0-100,
    "technical_seo_score": 0-100,
    "content_score": 0-100,
    "summary": "Professional executive summary (30-50 words).",
    "recommendations": [
        {
            "type": "critical|warning|info",
            "icon_color": "red|yellow|blue",
            "message": "High-impact actionable advice (max 70 chars)"
        },
        ... (Exactly 4 items)
    ],
    "section_analysis": [
        {
            "section": "Navigation & Header",
            "status": "Good|Needs Update|Critical",
            "analysis": "Evaluation of accessibility and navigation flow."
        },
        {
            "section": "Hero & Visuals",
            "status": "Good|Needs Update|Critical",
            "analysis": "Analysis of the main CTA and image optimization."
        },
        {
            "section": "Content & Readability",
            "status": "Good|Needs Update|Critical",
            "analysis": "Evaluation of E-E-A-T and semantic structure."
        },
        {
            "section": "Technical Metadata",
            "status": "Good|Needs Update|Critical",
            "analysis": "Audit of Title, Meta, and SEO tags."
        }
    ],
    "detailed_findings": {
        "strengths": ["string", "string", "string"],
        "weaknesses": ["string", "string", "string"],
        "opportunities": ["string", "string", "string"]
    }
}

Guidelines: Use technical terms like 'Lighthouse metrics', 'In-degree links', 'Schema.org', 'Core Web Vitals'. Balance scores for major brands.
EOT;
    }

    protected function cleanJsonResponse(string $response): string
    {
        return trim(preg_replace('/```json\s*|\s*```/', '', $response));
    }

    protected function generateFallbackAudit(array $siteData, array $metrics): array
    {
        $techScore = $metrics['has_title'] ? 70 : 40;

        return [
            'website_url' => $siteData['url'],
            'overall_score' => $techScore,
            'performance_score' => 80,
            'technical_seo_score' => $techScore,
            'content_score' => 60,
            'summary' => 'Automated basic scan completed. Some high-level AI insights are temporarily unavailable.',
            'recommendations' => [
                ['type' => 'critical', 'icon_color' => 'red', 'message' => 'Improve Title and Meta visibility.'],
                ['type' => 'warning', 'icon_color' => 'yellow', 'message' => 'Optimize image alt tags.'],
                ['type' => 'info', 'icon_color' => 'blue', 'message' => 'Audit internal link structure.'],
                ['type' => 'info', 'icon_color' => 'blue', 'message' => 'Verify mobile responsiveness.'],
            ],
            'section_analysis' => [
                ['section' => 'Navigation', 'status' => 'Good', 'analysis' => 'Basic navigation present.'],
                ['section' => 'Technical', 'status' => 'Needs Update', 'analysis' => 'Metadata optimization required.'],
            ],
            'detailed_findings' => [
                'strengths' => ['Website is reachable'],
                'weaknesses' => ['AI Analysis timed out'],
                'opportunities' => ['Rerun audit for better insights'],
            ],
        ];
    }
}
