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

    /**
     * Generate Dashboard AI Insights
     */
    public function generateDashboardInsights(array $userData): array
    {
        if (empty($this->apiKey)) {
            return ['error' => 'OpenAI API Key is missing.'];
        }

        $prompt = "You are an expert digital agency assistant. Analyze the client's current status and provide a simple summary, insights, and next steps for their dashboard.
        
        Client Data:
        " . json_encode($userData) . "
        
        Return the response in this JSON format:
        {
            \"summary\": \"A simple, friendly 1-2 sentence explanation of their current progress.\",
            \"insights\": [\"Insight 1\", \"Insight 2\"],
            \"next_steps\": [\"Step 1\", \"Step 2\"]
        }";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an AI assistant providing simple dashboard insights for digital agency clients.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            if (!$response->successful()) {
                Log::error('OpenAI Dashboard Insights Error: ' . $response->body());
                return ['error' => 'Failed to generate insights.'];
            }

            return json_decode($response->json()['choices'][0]['message']['content'], true);
        } catch (\Exception $e) {
            Log::error('OpenAI Service Exception: ' . $e->getMessage());
            return ['error' => 'An error occurred while generating insights.'];
        }
    }

    /**
     * Generate Personalized Proposal
     */
    public function generateProposal(array $leadData, array $serviceData): array
    {
        if (empty($this->apiKey)) {
            return ['error' => 'OpenAI API Key is missing.'];
        }

        $prompt = "You are an expert sales strategist for a digital agency. Create a personalized proposal based on the lead details and service offering.
        
        Lead Details: " . json_encode($leadData) . "
        Service Info: " . json_encode($serviceData) . "
        
        Return the response in this JSON format:
        {
            \"personalized_greeting\": \"...\",
            \"executive_summary\": \"...\",
            \"recommended_scope\": [\"Task 1\", \"Task 2\", ...],
            \"timeline_estimate\": \"e.g., 4-6 weeks\",
            \"pricing_recommendation\": \"e.g., Starts at $1,500/month based on needs\",
            \"why_choose_us\": \"A personalized reason\"
        }";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(45)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert sales strategist creating personalized proposals.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            return json_decode($response->json()['choices'][0]['message']['content'], true);
        } catch (\Exception $e) {
            Log::error('OpenAI Service Exception: ' . $e->getMessage());
            return ['error' => 'An error occurred while generating proposal.'];
        }
    }

    /**
     * Generate AI Summary for Monthly Reports
     */
    public function generateReportSummary(array $reportData): array
    {
        if (empty($this->apiKey)) {
            return ['error' => 'OpenAI API Key is missing.'];
        }

        $prompt = "You are a digital marketing manager reporting to a client. Summarize their monthly campaign progress and suggest actions.
        
        Report Data: " . json_encode($reportData) . "
        
        Return the response in this JSON format:
        {
            \"executive_summary\": \"...\",
            \"key_achievements\": [\"Achievement 1\", ...],
            \"suggested_actions\": [\"Action 1\", ...]
        }";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a digital marketing manager.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            return json_decode($response->json()['choices'][0]['message']['content'], true);
        } catch (\Exception $e) {
            Log::error('OpenAI Service Exception: ' . $e->getMessage());
            return ['error' => 'An error occurred while generating report summary.'];
        }
    }

    /**
     * Generate Chatbot Reply
     */
    public function generateChatbotReply(string $message, array $context): string
    {
        if (empty($this->apiKey)) {
            return $this->getMockResponse($message);
        }

        $systemPrompt = "You are the AI assistant for PPHJobDone, a digital marketing agency.
        You qualify leads, answer FAQs, and guide users. Be polite, professional, and concise.
        Context about services/pricing: " . json_encode($context);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(20)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $message],
                ],
            ]);

            if (!$response->successful()) {
                Log::error('OpenAI Chatbot Reply Error: ' . $response->body());
                // Fallback to mock response if API key is invalid or rate limited
                return $this->getMockResponse($message, $context);
            }

            return $response->json()['choices'][0]['message']['content'] ?? "I didn't quite catch that.";
        } catch (\Exception $e) {
            Log::error('OpenAI Service Exception: ' . $e->getMessage());
            return $this->getMockResponse($message, $context);
        }
    }

    /**
     * Temporary Mock Response for Testing Without API Key
     */
    protected function getMockResponse(string $message, array $context = []): string
    {
        $message = strtolower($message);
        
        if (str_contains($message, 'hello') || str_contains($message, 'hi')) {
            return "Hello! I am the AI assistant. How can I help you scale your business today?";
        }
        
        // Use context data (from database) for pricing and services
        if (str_contains($message, 'pricing') || str_contains($message, 'cost') || str_contains($message, 'price')) {
            $pricingInfo = "Here is our pricing for various services:\n\n";
            if (!empty($context['services'])) {
                foreach ($context['services'] as $service) {
                    $pricingInfo .= "🚀 " . strtoupper($service['name']) . "\n";
                    if (!empty($service['plans'])) {
                        foreach ($service['plans'] as $plan) {
                            $pricingInfo .= "   • {$plan}\n";
                        }
                    } else {
                        $pricingInfo .= "   • Custom Pricing\n";
                    }
                    $pricingInfo .= "\n";
                }
                return trim($pricingInfo);
            }
            return "Please contact our sales team for pricing details.";
        }
        
        if (str_contains($message, 'service') || str_contains($message, 'servic') || str_contains($message, 'campaign') || str_contains($message, 'campain')) {
            if (!empty($context['services'])) {
                $serviceInfo = "We offer the following digital marketing services:\n\n";
                foreach ($context['services'] as $service) {
                    $serviceInfo .= "✅ {$service['name']}\n";
                }
                return trim($serviceInfo);
            }
            return "We offer various digital marketing services. Please check our services page.";
        }

        return "This is a dummy response because the OpenAI API key is missing or invalid. You said: '{$message}'";
    }
}
