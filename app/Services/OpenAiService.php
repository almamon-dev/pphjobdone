<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key') ?? '';
    }

    /**
     * Generate SEO Recommendations using OpenAI
     */
    public function generateSeoRecommendations(array $siteData): array
    {
        if (empty($this->apiKey)) {
            return ['error' => 'OpenAI API Key is missing.'];
        }

        $prompt = $this->buildSeoPrompt($siteData);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'Application/json',
            ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert SEO auditor. Analyze the provided website data and return a detailed SEO audit in JSON format.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            if (!$response->successful()) {
                Log::error('OpenAI SEO Audit Error: ' . $response->body());
                return ['error' => 'Failed to generate SEO audit with OpenAI.'];
            }

            $data = $response->json();
            $content = json_decode($data['choices'][0]['message']['content'], true);

            $content['website_url'] = $siteData['url'];

            return $content;
        } catch (\Exception $e) {
            Log::error('OpenAI Service Exception: ' . $e->getMessage());
            return ['error' => 'An error occurred while generating SEO audit.'];
        }
    }

    /**
     * Build the prompt for OpenAI
     */
    protected function buildSeoPrompt(array $siteData): string
    {
        return "Please analyze the following website data and return a JSON object with a complete SEO audit.
        
        Website URL: {$siteData['url']}
        Title: {$siteData['title']}
        Meta Description: {$siteData['description']}
        Keywords: {$siteData['keywords']}
        Heading Tags (H1-H6): {$siteData['headings']}
        Content Snippet: {$siteData['content']}
        Image Count: {$siteData['image_count']}
        Internal/External Link Count: {$siteData['link_count']}
        Load Time: {$siteData['load_time']} ms
        Page Size: {$siteData['page_size']} bytes
        Script Count: {$siteData['script_count']}
        Style Count: {$siteData['style_count']}
        
        Return the response in this JSON format:
        {
            \"overall_score\": (number 0-100),
            \"technical_seo_score\": (number 0-100),
            \"performance_score\": (number 0-100),
            \"content_score\": (number 0-100),
            \"summary\": \"Brief executive summary of findings\",
            \"recommendations\": [
                { \"type\": \"critical|warning|info\", \"message\": \"...\" },
                ... (provide at least 4)
            ],
            \"section_analysis\": [
                { \"section\": \"Meta Data\", \"status\": \"Good|Needs Update|Critical\", \"analysis\": \"...\" },
                { \"section\": \"Content Audit\", \"status\": \"...\", \"analysis\": \"...\" },
                { \"section\": \"Performance\", \"status\": \"Good|Needs Update|Critical\", \"analysis\": \"...\" }
            ]
        }";
    }
}
