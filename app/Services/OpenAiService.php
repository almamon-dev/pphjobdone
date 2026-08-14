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
        if (!empty($this->apiKey)) {
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

                if ($response->successful()) {
                    $result = json_decode($response->json()['choices'][0]['message']['content'], true);
                    if (isset($result['summary']) && isset($result['insights']) && isset($result['next_steps'])) {
                        return $result;
                    }
                }
                Log::warning('OpenAI Dashboard Insights API call failed or returned incomplete format. Falling back to local data engine.');
            } catch (\Exception $e) {
                Log::warning('OpenAI Service Exception: ' . $e->getMessage() . '. Falling back to local data engine.');
            }
        }

        return $this->generateFallbackDashboardInsights($userData);
    }

    /**
     * Fallback AI engine generating insights based on real user data
     */
    protected function generateFallbackDashboardInsights(array $userData): array
    {
        $servicesCount = $userData['active_services_count'] ?? 0;
        $services = $userData['services'] ?? [];
        $latestAudit = $userData['latest_seo_audit'] ?? null;

        $totalCompleted = 0;
        $totalPending = 0;
        $totalInProgress = 0;
        $avgProgress = 0;

        if (!empty($services)) {
            $progresses = [];
            foreach ($services as $service) {
                $progresses[] = $service['overall_progress'] ?? 0;
                $totalCompleted += count($service['completed_tasks'] ?? []);
                $totalPending += count($service['pending_tasks'] ?? []);
                $totalInProgress += count($service['in_progress_tasks'] ?? []);
            }
            $avgProgress = count($progresses) > 0 ? round(array_sum($progresses) / count($progresses)) : 0;
        }

        // Summary statement
        if ($servicesCount > 0) {
            $summary = "Your active campaigns are currently at {$avgProgress}% completion across {$servicesCount} service" . ($servicesCount > 1 ? "s" : "") . ". Operations and task deliverables are progressing smoothly according to schedule.";
        } elseif (!empty($latestAudit)) {
            $score = $latestAudit['score'] ?? 'N/A';
            $summary = "Your latest SEO audit for {$latestAudit['url']} is complete with an overall health score of {$score}/100. Initialize a service campaign to optimize critical areas.";
        } else {
            $summary = "Welcome to your AI Client Dashboard! You currently have no active service subscriptions or recent SEO audits. Explore our services to boost your web performance.";
        }

        // Key Insights
        $insights = [];
        if ($servicesCount > 0) {
            $serviceTitles = array_column($services, 'title');
            $serviceStr = implode(', ', array_slice($serviceTitles, 0, 2));
            $insights[] = "Currently running {$servicesCount} active service subscription" . ($servicesCount > 1 ? "s" : "") . " including {$serviceStr}.";

            if ($totalCompleted > 0) {
                $insights[] = "{$totalCompleted} project task milestone" . ($totalCompleted > 1 ? "s have" : " has") . " been successfully completed and verified.";
            }
            if ($totalInProgress > 0 || $totalPending > 0) {
                $remaining = $totalInProgress + $totalPending;
                $insights[] = "{$remaining} deliverable" . ($remaining > 1 ? "s are" : " is") . " actively in progress or scheduled for execution.";
            } else if ($totalCompleted == 0) {
                $insights[] = "Services are queued and initial campaign setup tasks will begin shortly.";
            }
        } else {
            $insights[] = "No active service packages are currently active for your account.";
        }

        if (!empty($latestAudit)) {
            $url = $latestAudit['url'] ?? 'your website';
            $score = $latestAudit['score'] ?? 'N/A';
            $insights[] = "Latest SEO audit for {$url} scored {$score}/100, highlighting targeted areas for search visibility.";
        } else {
            $insights[] = "No SEO audits found yet. Running a new site audit provides instant technical SEO recommendations.";
        }

        // Recommended Next Steps
        $nextSteps = [];
        if ($servicesCount > 0) {
            foreach ($services as $service) {
                if (!empty($service['pending_tasks'])) {
                    $taskName = $service['pending_tasks'][0];
                    $nextSteps[] = "Review upcoming milestone deliverable: '{$taskName}' under {$service['title']}.";
                    break;
                }
            }
            if (empty($nextSteps)) {
                $nextSteps[] = "Track service progress and task updates in your active dashboard portal.";
            }
            $nextSteps[] = "Connect with your assigned project lead via instant messaging for direct support.";
        } else {
            $nextSteps[] = "Browse our SEO, Content, and PPC packages to launch a customized campaign.";
        }

        if (empty($latestAudit)) {
            $nextSteps[] = "Run a free AI SEO audit on your site to identify ranking and traffic opportunities.";
        } elseif (count($nextSteps) < 2) {
            $nextSteps[] = "Review technical fixes recommended in your latest audit for {$latestAudit['url']}.";
        }

        return [
            'summary' => $summary,
            'insights' => array_values($insights),
            'next_steps' => array_values($nextSteps),
        ];
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
     * Generate Chatbot Reply with Lead Extraction & Qualification
     */
    public function generateChatbotReply(string $message, array $context): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockResponseWithLeadInfo($message, $context);
        }

        $systemPrompt = "You are the AI sales & support assistant for PPHJobDone, a digital marketing agency.
        Your goals:
        1. Answer user questions about services, pricing, and campaign packages using the provided context.
        2. Politely collect lead information (name, email, phone, company/website, service needed, budget).
        3. Qualify the lead:
           - 'Hot': Provided contact info (email/phone) AND clear service need or budget > $500.
           - 'Warm': Provided email or name, interested in specific service.
           - 'Cold': General questions, no contact details provided yet.

        Context: " . json_encode($context) . "

        Return a JSON object in this format:
        {
            \"reply\": \"Conversational reply text\",
            \"lead_info\": {
                \"name\": \"extracted name or null\",
                \"email\": \"extracted email or null\",
                \"phone\": \"extracted phone or null\",
                \"company_name\": \"extracted company/website or null\",
                \"service_interest\": \"extracted service interest or null\",
                \"budget\": \"extracted budget or null\",
                \"qualification_status\": \"Hot|Warm|Cold\",
                \"qualification_summary\": \"Short explanation of qualification\"
            }
        }";

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
                'response_format' => ['type' => 'json_object'],
            ]);

            if (!$response->successful()) {
                Log::error('OpenAI Chatbot Reply Error: ' . $response->body());
                return $this->getMockResponseWithLeadInfo($message, $context);
            }

            $data = json_decode($response->json()['choices'][0]['message']['content'], true);
            
            return [
                'reply' => $data['reply'] ?? "How else can I help you today?",
                'lead_info' => $data['lead_info'] ?? [
                    'qualification_status' => 'Cold',
                    'qualification_summary' => 'General inquiry'
                ]
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI Service Exception: ' . $e->getMessage());
            return $this->getMockResponseWithLeadInfo($message, $context);
        }
    }

    /**
     * Mock Response with Lead Info for Testing Without API Key
     */
    protected function getMockResponseWithLeadInfo(string $message, array $context = []): array
    {
        $lowerMsg = strtolower($message);
        
        // Extract basic lead details if present in message
        $email = null;
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $message, $matches)) {
            $email = $matches[0];
        }

        $phone = null;
        if (preg_match('/(\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}/', $message, $matches)) {
            $phone = $matches[0];
        }

        $budget = null;
        if (preg_match('/\$?\d+(?:,\d+)*(?:\s*k|\s*thousand|\s*dollars|\/mo|\/month)?/i', $message, $matches)) {
            $budget = $matches[0];
        }

        $serviceInterest = null;
        if (str_contains($lowerMsg, 'seo')) $serviceInterest = 'SEO Optimization';
        elseif (str_contains($lowerMsg, 'link')) $serviceInterest = 'Link Building';
        elseif (str_contains($lowerMsg, 'guest')) $serviceInterest = 'Guest Posting';
        elseif (str_contains($lowerMsg, 'content') || str_contains($lowerMsg, 'writing')) $serviceInterest = 'Content Writing';

        // Determine qualification status
        $qualificationStatus = 'Cold';
        $summary = 'Initial casual inquiry.';

        if ($email || $phone) {
            if ($budget || $serviceInterest) {
                $qualificationStatus = 'Hot';
                $summary = 'High intent lead with contact information and service interest.';
            } else {
                $qualificationStatus = 'Warm';
                $summary = 'Lead provided contact info but budget/service details are pending.';
            }
        } elseif ($serviceInterest || $budget) {
            $qualificationStatus = 'Warm';
            $summary = 'Expressed interest in service/budget but contact info is missing.';
        }

        $reply = "Hello! I'm the PPHJobDone AI Assistant. How can I help scale your business today?";

        if (str_contains($lowerMsg, 'audit') || str_contains($lowerMsg, 'how ai seo') || str_contains($lowerMsg, 'works')) {
            $reply = "🔍 How AI SEO Audit Works:\n\n1. Enter your website URL in our AI SEO Audit tool.\n2. Our AI scans your site's technical structure, meta tags, content score, and load performance.\n3. A full PDF Report is generated with overall scores (0-100) and step-by-step recommendations.\n4. The report is emailed directly to your inbox and saved in your client dashboard!\n\nWould you like to run a free audit for your website now?";
        } elseif (str_contains($lowerMsg, 'pricing') || str_contains($lowerMsg, 'cost') || str_contains($lowerMsg, 'price')) {
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
                $reply = trim($pricingInfo) . "\n\nWould you like me to tailor a custom plan for your budget?";
            } else {
                $reply = "Our plans start at $199/mo for SEO campaigns. Would you like to share your email so our team can send a customized proposal?";
            }
        } elseif (str_contains($lowerMsg, 'service') || str_contains($lowerMsg, 'servic') || str_contains($lowerMsg, 'campaign')) {
            if (!empty($context['services'])) {
                $serviceInfo = "We offer the following digital marketing services:\n\n";
                foreach ($context['services'] as $service) {
                    $serviceInfo .= "✅ {$service['name']}\n";
                }
                $reply = trim($serviceInfo) . "\n\nWhich service are you interested in?";
            } else {
                $reply = "We offer SEO, Link Building, and Digital Marketing campaigns. What goals are you aiming for?";
            }
        } elseif ($email) {
            $reply = "Thank you! I've noted down your email ($email). Our team will reach out with details shortly. Is there anything specific about your project you'd like us to know?";
        }

        return [
            'reply' => $reply,
            'lead_info' => [
                'name' => null,
                'email' => $email,
                'phone' => $phone,
                'company_name' => null,
                'service_interest' => $serviceInterest,
                'budget' => $budget,
                'qualification_status' => $qualificationStatus,
                'qualification_summary' => $summary,
            ]
        ];
    }

}
