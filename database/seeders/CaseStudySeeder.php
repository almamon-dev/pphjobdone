<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use Illuminate\Database\Seeder;

class CaseStudySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultChallengeDescription = "The client was struggling with low organic visibility in a competitive niche. Their product and service pages were not ranking for key commercial search terms, and organic traffic was minimal. They needed a comprehensive SEO and digital marketing strategy to increase qualified traffic and improve conversion rates.";

        $defaultChallengeItems = [
            ["icon" => "Search", "text" => "Low keyword rankings for high-intent commercial search terms"],
            ["icon" => "RefreshCw", "text" => "Minimal organic traffic to product and landing pages"],
            ["icon" => "Layers", "text" => "Under-optimized content structure and on-page SEO gaps"],
            ["icon" => "ShoppingCart", "text" => "Low conversion rate despite existing site visits"],
        ];

        $defaultApproachDescription = "We followed a structured, insight-driven SEO process tailored to the client's business goals. Each step focused on improving search visibility, attracting high-intent users, and optimizing the journey from discovery to conversion.";

        $defaultApproachItems = [
            "Conducted Detailed Keyword Research Focusing On Commercial Intent And Long-Tail Opportunities",
            "Optimized Landing Pages With Improved Meta Data, Descriptions, And Schema Markup",
            "Created A Content Calendar Targeting Buyer Journey Keywords",
            "Built High-Authority Backlinks Through Strategic Outreach And Content Partnerships",
            "Implemented Technical SEO Improvements Including Site Speed Optimization And Mobile Enhancements",
        ];

        $defaultResultsDescription = "Our strategic execution delivered consistent and measurable growth across key performance metrics. The client experienced significant improvements in traffic quality, keyword rankings, revenue, and overall site performance within a short timeframe.";

        $defaultResultsItems = [
            "120% Increase In Organic Traffic Within 6 Months",
            "45 Keywords Ranking In Top 3 Positions On Google",
            "85% Increase In Organic Revenue",
            "Page Load Time Reduced By 40%",
            "Mobile Conversion Rate Improved By 32%",
        ];

        $caseStudies = [
            [
                'id' => 1,
                'category' => 'E-commerce',
                'title' => 'SEO Monthly + Content Writing',
                'stats' => '+120% Organic Traffic in 6 Months',
                'image' => '/storage/case-studies/p1.webp',
                'video_bg_image' => '/storage/case-studies/videoBg.webp',
                'client_type' => 'Online Retailer',
                'location' => 'United States',
                'service_provided' => 'SEO Monthly + Content Writing',
                'duration' => '6 Months',
                'challenge_description' => $defaultChallengeDescription,
                'challenge_items' => $defaultChallengeItems,
                'approach_description' => $defaultApproachDescription,
                'approach_items' => $defaultApproachItems,
                'results_description' => $defaultResultsDescription,
                'results_items' => $defaultResultsItems,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'category' => 'SaaS',
                'title' => 'Google Ads PPC Campaign',
                'stats' => '+85% Conversion Rate Increase',
                'image' => '/storage/case-studies/p2.webp',
                'video_bg_image' => '/storage/case-studies/videoBg.webp',
                'client_type' => 'Software Company',
                'location' => 'Germany',
                'service_provided' => 'Google Ads PPC + Landing Page Optimization',
                'duration' => '3 Months',
                'challenge_description' => 'The SaaS client was experiencing high customer acquisition costs (CAC) and low ad conversion efficiency across Google Ads campaigns.',
                'challenge_items' => [
                    ["icon" => "Search", "text" => "High cost per click (CPC) on competitive B2B SaaS keywords"],
                    ["icon" => "RefreshCw", "text" => "Low conversion rates on legacy landing pages"],
                    ["icon" => "Layers", "text" => "Unoptimized ad copy and lack of A/B split testing"],
                ],
                'approach_description' => 'We restructured ad campaign groups, overhauled ad copy for value propositions, and designed high-converting dedicated landing pages.',
                'approach_items' => [
                    "Restructured Google Search & Display ad campaigns into single-intent ad groups",
                    "A/B tested 10+ high-converting landing page headlines and CTA variations",
                    "Implemented precise conversion tracking and automated bidding strategies",
                ],
                'results_description' => 'Remarkable ROI improvements within 90 days with reduced CAC and surging demo signups.',
                'results_items' => [
                    "85% Increase In Trial & Demo Conversion Rate",
                    "38% Reduction In Customer Acquisition Cost (CAC)",
                    "3.4x Return On Ad Spend (ROAS)",
                ],
                'is_active' => true,
            ],
            [
                'id' => 3,
                'category' => 'Real Estate',
                'title' => 'Social Media Marketing',
                'stats' => '20k+ Leads Generated in Year',
                'image' => '/storage/case-studies/p3.webp',
                'video_bg_image' => '/storage/case-studies/videoBg.webp',
                'client_type' => 'Real Estate Agency',
                'location' => 'United Kingdom',
                'service_provided' => 'Social Media Marketing & Lead Generation',
                'duration' => '12 Months',
                'challenge_description' => 'The client needed steady, high-intent lead flow for luxury real estate property developments across Meta and LinkedIn.',
                'challenge_items' => [
                    ["icon" => "Search", "text" => "Lack of consistent high-ticket buyer lead pipeline"],
                    ["icon" => "RefreshCw", "text" => "Low engagement on organic social media channels"],
                ],
                'approach_description' => 'Executed targeted multi-channel lead magnet campaigns with video property walkthroughs and automated instant lead forms.',
                'approach_items' => [
                    "Produced high-resolution video reels and carousel ads showcasing key property features",
                    "Targeted high-net-worth demographic segments on Meta & LinkedIn",
                    "Integrated instant CRM sync for immediate sales team follow-up",
                ],
                'results_description' => 'Generated over 20,000 qualified buyer leads resulting in record property sales volume.',
                'results_items' => [
                    "20,000+ Qualified Leads Generated In 12 Months",
                    "240% Growth In Social Media Brand Following",
                    "$14M+ In Closed Real Estate Sales Volume",
                ],
                'is_active' => true,
            ],
            [
                'id' => 4,
                'category' => 'Fintech',
                'title' => 'UI/UX Design Revamp',
                'stats' => 'Award Winning App Design',
                'image' => '/storage/case-studies/p4.webp',
                'video_bg_image' => '/storage/case-studies/videoBg.webp',
                'client_type' => 'Fintech Platform',
                'location' => 'Canada',
                'service_provided' => 'UI/UX Redesign & Web App Optimization',
                'duration' => '4 Months',
                'challenge_description' => 'A complex user interface caused high user drop-off during onboarding for the mobile investment app.',
                'challenge_items' => [
                    ["icon" => "Layers", "text" => "High user drop-off rate during multi-step KYC onboarding"],
                    ["icon" => "ShoppingCart", "text" => "Cluttered dashboard causing user confusion"],
                ],
                'approach_description' => 'Redesigned the user experience from the ground up focusing on sleek, dark-mode modern glassmorphic UI aesthetics and friction-free flows.',
                'approach_items' => [
                    "Conducted in-depth user testing and behavioral heat-mapping",
                    "Created a unified modern design system with fluid animations",
                    "Simplified the KYC verification onboarding flow into 3 quick steps",
                ],
                'results_description' => 'Significant boost in user retention and awarded top industry design honors.',
                'results_items' => [
                    "65% Increase In Completed User Onboardings",
                    "4.8/5 Star Average App Store User Rating",
                    "Winner Of Best Fintech UI/UX Award 2025",
                ],
                'is_active' => true,
            ],
            [
                'id' => 5,
                'category' => 'Healthcare',
                'title' => 'Local SEO Strategy',
                'stats' => 'Top 3 Ranking for Keywords',
                'image' => '/storage/case-studies/p5.webp',
                'video_bg_image' => '/storage/case-studies/videoBg.webp',
                'client_type' => 'Medical Clinic Network',
                'location' => 'Australia',
                'service_provided' => 'Local SEO & Google Business Profile Management',
                'duration' => '6 Months',
                'challenge_description' => 'Local clinic branches were invisible on Google Maps and Local Map Packs for surrounding patient search queries.',
                'challenge_items' => [
                    ["icon" => "Search", "text" => "Poor Google Map Pack positions for local healthcare keywords"],
                    ["icon" => "RefreshCw", "text" => "Inconsistent NAP (Name, Address, Phone) citations across directories"],
                ],
                'approach_description' => 'Optimized Google Business Profiles across all locations, built targeted local citations, and generated authentic patient reviews.',
                'approach_items' => [
                    "Optimized Google Business Profiles with geo-targeted photos and posts",
                    "Cleaned up 100+ local directory citations for NAP consistency",
                    "Launched automated review request campaigns via SMS/Email",
                ],
                'results_description' => 'Achieved top 3 Google Map Pack positions for 30+ medical keywords and doubled patient appointment bookings.',
                'results_items' => [
                    "Top 3 Ranking For 30+ High-Volume Local Keywords",
                    "110% Increase In Direct Phone Calls & Directions Requests",
                    "95% Increase In Monthly Patient Appointments",
                ],
                'is_active' => true,
            ],
            [
                'id' => 6,
                'category' => 'E-commerce',
                'title' => 'SEO Monthly + Content Writing',
                'stats' => '+120% Organic Traffic in 6 Months',
                'image' => '/storage/case-studies/p1.webp',
                'video_bg_image' => '/storage/case-studies/videoBg.webp',
                'client_type' => 'Fashion Retailer',
                'location' => 'United States',
                'service_provided' => 'SEO Monthly + Content Strategy',
                'duration' => '6 Months',
                'challenge_description' => $defaultChallengeDescription,
                'challenge_items' => $defaultChallengeItems,
                'approach_description' => $defaultApproachDescription,
                'approach_items' => $defaultApproachItems,
                'results_description' => $defaultResultsDescription,
                'results_items' => $defaultResultsItems,
                'is_active' => true,
            ],
            [
                'id' => 7,
                'category' => 'SaaS',
                'title' => 'Google Ads PPC Campaign',
                'stats' => '+85% Conversion Rate Increase',
                'image' => '/storage/case-studies/p2.webp',
                'video_bg_image' => '/storage/case-studies/videoBg.webp',
                'client_type' => 'Cloud Management SaaS',
                'location' => 'Germany',
                'service_provided' => 'PPC & Growth Marketing',
                'duration' => '5 Months',
                'challenge_description' => 'Needed rapid user acquisition to meet quarterly venture growth targets.',
                'challenge_items' => $defaultChallengeItems,
                'approach_description' => $defaultApproachDescription,
                'approach_items' => $defaultApproachItems,
                'results_description' => $defaultResultsDescription,
                'results_items' => $defaultResultsItems,
                'is_active' => true,
            ],
            [
                'id' => 8,
                'category' => 'Real Estate',
                'title' => 'Social Media Marketing',
                'stats' => '20k+ Leads Generated in Year',
                'image' => '/storage/case-studies/p3.webp',
                'video_bg_image' => '/storage/case-studies/videoBg.webp',
                'client_type' => 'Luxury Property Developers',
                'location' => 'United Arab Emirates',
                'service_provided' => 'Social Marketing & Lead Campaigns',
                'duration' => '12 Months',
                'challenge_description' => 'Targeting international property investors for high-rise commercial developments.',
                'challenge_items' => $defaultChallengeItems,
                'approach_description' => $defaultApproachDescription,
                'approach_items' => $defaultApproachItems,
                'results_description' => $defaultResultsDescription,
                'results_items' => $defaultResultsItems,
                'is_active' => true,
            ],
        ];

        foreach ($caseStudies as $data) {
            CaseStudy::updateOrCreate(['id' => $data['id']], $data);
        }
    }
}
