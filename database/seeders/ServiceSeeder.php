<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing services to ensure only these 6 high-quality ones are present
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Service::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $services = [
            [
                'title' => 'Technical SEO Dominance',
                'subtitle' => 'Eliminate indexing barriers and fuel your organic growth.',
                'description' => 'Our technical SEO service dives deep into your site\'s architecture to fix the hidden structural issues that hold your rankings back. We optimize crawl efficiency, site structures, and internal link logic for maximum search visibility.',
                'status' => true,
                'faqs' => [
                    ['question' => 'How long does a technical audit take?', 'answer' => 'A full audit typically takes 5-7 business days, followed by an intensive implementation phase.'],
                    ['question' => 'Do you fix Schema markup?', 'answer' => 'Yes, we implement advanced JSON-LD structured data for rich snippets and better CTR.'],
                    ['question' => 'What tools do you use?', 'answer' => 'We use enterprise-grade tools like Screaming Frog and Ahrefs to map out every issue.'],
                ],
                'process_steps' => [
                    ['title' => 'Deep Crawl Analysis', 'description' => 'Mapping out crawl budget bottlenecks and indexing errors.'],
                    ['title' => 'Semantic Data Strategy', 'description' => 'Implementing JSON-LD schema for context-aware search results.'],
                    ['title' => 'Infrastructure Tuning', 'description' => 'Optimizing rendering paths and scripts for speed and discoverability.'],
                ],
                'section_one' => [
                    'title' => 'The Invisible Advantage',
                    'subtitle' => 'Engineering Excellence',
                    'description' => 'Before content or links, your site needs a flawless foundation. We ensure search engines love your architecture as much as your users do.',
                ],
                'section_two' => [
                    'title' => 'Index Every Asset',
                    'subtitle' => 'Zero Bottlenecks',
                    'description' => 'We optimize your robots.txt, sitemaps, and internal linking to ensure Google discovers every high-value page you own.',
                ],
                'benefits' => [
                    [
                        'title' => 'Performance',
                        'points' => ['Faster Discovery', 'Optimized Crawl Budget', 'Rich Snippet Eligibility'],
                    ],
                    [
                        'title' => 'Efficiency',
                        'points' => ['Reduced Duplicate Content', 'Proper Canonicalization', 'Clean URL Structure'],
                    ],
                ],
                'timeline' => [
                    [
                        'title' => 'Audit & Strategy',
                        'duration' => 'Week 1-2',
                        'price' => '$1,500',
                        'items' => ['Comprehensive Technical Audit', 'Critical Error Patching', 'Schema Architecture Setup'],
                    ],
                    [
                        'title' => 'Implementation Phase',
                        'duration' => 'Week 3-4',
                        'price' => '$2,500',
                        'items' => ['Site Structure Refinement', 'Mobile-First Optimization', 'Dynamic Rendering Tuning'],
                    ],
                ],
                'expect_results' => [
                    ['title' => 'Crawl Efficiency', 'value' => '+250%', 'subtitle' => 'Discovery Rate'],
                    ['title' => 'Ranking Boost', 'value' => 'Top 5', 'subtitle' => 'Target Growth'],
                    ['title' => 'SEO Score', 'value' => '98/100', 'subtitle' => 'Lighthouse'],
                    ['title' => 'Page Load Time', 'value' => '< 1.8s', 'subtitle' => 'Avg Rendering'],
                ],
            ],
            [
                'title' => 'Authority Link Building',
                'subtitle' => 'High-DR editorial placements that drive sustainable rankings.',
                'description' => 'Authority is the currency of SEO. We build the bridges that connect your brand to industry-leading publications through manual outreach and high-quality guest editorial placements.',
                'status' => true,
                'faqs' => [
                    ['question' => 'Are these links permanent?', 'answer' => 'Yes, our placements are lifetime editorial links on real websites with genuine traffic.'],
                    ['question' => 'Is this safe?', 'answer' => 'We use 100% white-hat manual outreach. No PBNs, no spam, no automated tools.'],
                ],
                'process_steps' => [
                    ['title' => 'Niche Prospecting', 'description' => 'Filtering thousands of sites to find the ones that perfectly match your industry.'],
                    ['title' => 'Outreach Strategy', 'description' => 'Personalized pitching to site owners and editors for natural placements.'],
                    ['title' => 'Live Link Audit', 'description' => 'Verifying link attributes and ensuring they stay live and indexed.'],
                ],
                'section_one' => [
                    'title' => 'The Ranking Signal That Matters',
                    'subtitle' => 'Natural Authority',
                    'description' => 'Search engines trust websites that other trusted sites link to. We earn that trust for you.',
                ],
                'section_two' => [
                    'title' => 'Editorial Integrity',
                    'subtitle' => 'Real Sites, Real Traffic',
                    'description' => 'Every link we build comes from a site with actual keyword rankings and monthly visitors.',
                ],
                'benefits' => [
                    [
                        'title' => 'Authority',
                        'points' => ['Higher Domain Rating (DR)', 'Improved Keyword Reach', 'Niche-Specific Trust'],
                    ],
                    [
                        'title' => 'Traffic',
                        'points' => ['Referral Visitors', 'Sustainable Rank Growth', 'Brand Awareness'],
                    ],
                ],
                'timeline' => [
                    [
                        'title' => 'Prospecting & Content',
                        'duration' => 'Week 1-2',
                        'price' => '$1,200',
                        'items' => ['Competitor Gap Analysis', 'Guest Post Content Creation', 'Initial Outreach Campaigns'],
                    ],
                    [
                        'title' => 'Acquisition Phase',
                        'duration' => 'Ongoing',
                        'price' => '$2,500/Mo',
                        'items' => ['Link Placement Monitoring', 'Fresh Outreach Cycles', 'Success Reporting'],
                    ],
                ],
                'expect_results' => [
                    ['title' => 'Domain Authority', 'value' => '+15', 'subtitle' => 'Within 90 Days'],
                    ['title' => 'Ref. Domains', 'value' => '25+', 'subtitle' => 'High Quality'],
                    ['title' => 'Traffic Reach', 'value' => '+120%', 'subtitle' => 'Organic Uplift'],
                ],
            ],
            [
                'title' => 'Core Web Vitals Mastery',
                'subtitle' => 'Transform your site into a lightning-fast experience.',
                'description' => 'A fast site is a converting site. We optimize your Core Web Vitals—LCP, FID, and CLS—to provide a seamless experience that satisfies both Google’s algorithms and your visitors.',
                'status' => true,
                'faqs' => [
                    ['question' => 'What is Core Web Vitals?', 'answer' => 'Google’s official metrics for measuring user experience in terms of loading, interactivity, and visual stability.'],
                ],
                'process_steps' => [
                    ['title' => 'Payload Optimization', 'description' => 'Aggressive minification and compression of all site assets.'],
                    ['title' => 'Render Path Tuning', 'description' => 'Optimizing the critical CSS path and deferring non-essential scripts.'],
                    ['title' => 'Modern Image Stack', 'description' => 'Converting assets into next-gen formats and implementing advanced lazy loading.'],
                ],
                'section_one' => [
                    'title' => 'Milliseconds Equal Money',
                    'subtitle' => 'The Speed Engine',
                    'description' => 'A split-second delay can cost millions in revenue. We make your site instant.',
                ],
                'section_two' => [
                    'title' => 'Pass the Vitals Test',
                    'subtitle' => 'Ranking Bonus',
                    'description' => 'Google rewards fast sites. We ensure you hit straight greens across all performance metrics.',
                ],
                'benefits' => [
                    [
                        'title' => 'Engagement',
                        'points' => ['Lower Bounce Rate', 'Higher Time on Site', 'Better Ad Quality Scores'],
                    ],
                    [
                        'title' => 'Conversion',
                        'points' => ['Seamless Checkout Flow', 'Zero Interruption UX'],
                    ],
                ],
                'timeline' => [
                    [
                        'title' => 'Quick Wins',
                        'duration' => 'Week 1',
                        'price' => '$1,000',
                        'items' => ['CDN Integration', 'Image Compression Stack', 'Caching Engine Setup'],
                    ],
                ],
                'expect_results' => [
                    ['title' => 'Google Score', 'value' => '95+', 'subtitle' => 'Mobile/Desktop'],
                    ['title' => 'LCP Speed', 'value' => '< 1.2s', 'subtitle' => 'Lightning Fast'],
                    ['title' => 'CLS Score', 'value' => '0.01', 'subtitle' => 'Rock Solid'],
                ],
            ],
            [
                'title' => 'Semantic Content Strategy',
                'subtitle' => 'Topical authority that ranks and converts.',
                'description' => 'We don’t just write blogs; we build topical dominance. Every piece is engineered with TF-IDF optimization and search intent mapping to ensure you satisfy the user and the search algorithm.',
                'status' => true,
                'faqs' => [
                    ['question' => 'Who writes the content?', 'answer' => 'Subject matter experts with a deep understanding of SEO copywriting best practices.'],
                ],
                'process_steps' => [
                    ['title' => 'Topic Cluster Mapping', 'description' => 'Building internal link power through semantic keyword grouping.'],
                    ['title' => 'Intent Optimization', 'description' => 'Drafting content that perfectly aligns with the searcher’s stage in the buyer journey.'],
                    ['title' => 'Entity SEO Implementation', 'description' => 'Optimizing for knowledge graphs and AI-driven search results.'],
                ],
                'section_one' => [
                    'title' => 'Be the Expert',
                    'subtitle' => 'Topical Sovereignty',
                    'description' => 'Search engines rank experts, not just pages. We build your authority through comprehensive content ecosystems.',
                ],
                'section_two' => [
                    'title' => 'Content with ROI',
                    'subtitle' => 'Conversion Focussed',
                    'description' => 'We write for humans, optimized for bots, designed for sales.',
                ],
                'benefits' => [
                    [
                        'title' => 'Visibility',
                        'points' => ['Topical Authority', 'Long-Tail Rank Growth', 'Knowledge Graph Presence'],
                    ],
                ],
                'timeline' => [
                    [
                        'title' => 'Strategy Phase',
                        'duration' => 'Week 1',
                        'price' => '$1,800',
                        'items' => ['Topical Gap Analysis', 'Editorial Roadmap Building', 'Keyword Intelligence'],
                    ],
                ],
                'expect_results' => [
                    ['title' => 'Keyword Coverage', 'value' => '+400%', 'subtitle' => 'Visibility Scope'],
                    ['title' => 'Engagement Read', 'value' => '3m+', 'subtitle' => 'Avg Time'],
                ],
            ],
            [
                'title' => 'Local SEO Supremacy',
                'subtitle' => 'Dominate your local market and the Pack 3.',
                'description' => 'Own the "near me" searches. We optimize your local ecosystem—from Google Business Profile to localized landing pages—to drive high-intent store visits and calls.',
                'status' => true,
                'faqs' => [
                    ['question' => 'What is the Pack 3?', 'answer' => 'The top three local results shown on Google Maps above organic results.'],
                ],
                'process_steps' => [
                    ['title' => 'GBP Optimization', 'description' => 'Professional audit and enrichment of your Google Business Profile.'],
                    ['title' => 'Localized Citations', 'description' => 'Consistency management across 60+ high-authority geo-directories.'],
                    ['title' => 'Geographic Content', 'description' => 'Building authority for specific local areas and neighborhoods.'],
                ],
                'section_one' => [
                    'title' => 'Get Found "Near Me"',
                    'subtitle' => 'Localized Reach',
                    'description' => 'When local customers look for your services, we make sure you are the undisputed first choice.',
                ],
                'section_two' => [
                    'title' => 'Review Velocity',
                    'subtitle' => 'Trust Building',
                    'description' => 'We help you implement systems to capture and display the winning customer social proof you deserve.',
                ],
                'benefits' => [
                    [
                        'title' => 'Foot Traffic',
                        'points' => ['Google Maps Dominance', 'Call Velocity Increase', 'Direction Requests'],
                    ],
                ],
                'timeline' => [
                    [
                        'title' => 'Cleanup Phase',
                        'duration' => 'Month 1',
                        'price' => '$600',
                        'items' => ['Citation Standardizing', 'GBP Setup & Audit', 'Local Keyword Research'],
                    ],
                ],
                'expect_results' => [
                    ['title' => 'Map Reach', 'value' => 'Top 3', 'subtitle' => 'Consistent Pack'],
                    ['title' => 'Direction Reqs', 'value' => '+80%', 'subtitle' => 'Engagement'],
                ],
            ],
            [
                'title' => 'Performance Marketing (PPC)',
                'subtitle' => 'Surgical precision for instant, scalable ROI.',
                'description' => 'Stop burning money on ads. We manage Google Ads and Meta campaigns with a ruthless focus on conversions, optimizing every cent of your ad spend for the highest possible return on ad spend (ROAS).',
                'status' => true,
                'faqs' => [
                    ['question' => 'How much should I spend?', 'answer' => 'We recommend a starting ad spend of at least $1,500/mo to gather enough data for rapid optimization.'],
                ],
                'process_steps' => [
                    ['title' => 'Funnel Architecture', 'description' => 'Building non-leaky tracking systems and high-converting landing pages.'],
                    ['title' => 'Algorithmic Bidding', 'description' => 'Leveraging machine learning for real-time bid adjustments and maximum efficiency.'],
                    ['title' => 'Creative Iteration', 'description' => 'Rigorous A/B testing of hooks, copy, and visual assets.'],
                ],
                'section_one' => [
                    'title' => 'Scalable Growth On Demand',
                    'subtitle' => 'Predictable Revenue',
                    'description' => 'Turn your ad spend into an investment vehicle with our data-first performance marketing.',
                ],
                'section_two' => [
                    'title' => 'Beyond the Click',
                    'subtitle' => 'Conversion Focussed',
                    'description' => 'We care about your cost-per-acquisition (CPA). If it doesn’t lead to a lead or a sale, we cut it.',
                ],
                'benefits' => [
                    [
                        'title' => 'Speed',
                        'points' => ['Instant Traffic', 'Rapid Market Insights', 'Immediate Lead Gen'],
                    ],
                ],
                'timeline' => [
                    [
                        'title' => 'Account Setup',
                        'duration' => 'Week 1',
                        'price' => '$1,200+',
                        'items' => ['Tracking Setup', 'Campaign Buildout', 'Asset Production'],
                    ],
                ],
                'expect_results' => [
                    ['title' => 'ROAS', 'value' => '400%+', 'subtitle' => 'Target Growth'],
                    ['title' => 'Lead Quality', 'value' => 'High', 'subtitle' => 'Optimized Funnel'],
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            $serviceData['slug'] = Str::slug($serviceData['title']);
            $serviceData['created_at'] = now();
            $serviceData['updated_at'] = now();
            Service::create($serviceData);
        }
    }
}
