<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing services to ensure only these 4 are present
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Service::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $services = [
            [
                'title' => 'Monthly SEO',
                'subtitle' => 'Comprehensive monthly SEO management to climb the search rankings.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Sample Video Link
                'status' => true,
                'pricing_plans' => [
                    [
                        'name' => 'Starter SEO',
                        'price' => '499',
                        'features' => ['5 Keywords', 'On-Page Optimization', 'Monthly Report'],
                    ],
                    [
                        'name' => 'Growth SEO',
                        'price' => '899',
                        'features' => ['15 Keywords', 'Content Creation', 'Backlink Building'],
                    ],
                    [
                        'name' => 'Enterprise SEO',
                        'price' => '1499',
                        'features' => ['Unlimited Keywords', 'Technical Audit', 'Competitor Analysis'],
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'How long does it take to see results?',
                        'answer' => 'SEO is a long-term strategy. Typically, you will start seeing significant movements in 3-6 months.',
                    ],
                    [
                        'question' => 'Do you provide weekly updates?',
                        'answer' => 'We provide comprehensive monthly reports, but our dashboard is updated weekly.',
                    ],
                ],
                'process_steps' => [
                    ['title' => 'Audit', 'description' => 'We analyze your website\'s current SEO health.'],
                    ['title' => 'Optimization', 'description' => 'Fixing technical issues and optimizing content.'],
                    ['title' => 'Monitoring', 'description' => 'Tracking rankings and adjusting strategy.'],
                ],
                'section_one' => [
                    'title' => 'Dominate Search Results',
                    'subtitle' => 'Rank Higher, Grow Faster',
                    'description' => 'Our monthly SEO services are designed to give your business a sustainable competitive advantage in search engines.',
                ],
                'section_two' => [
                    'title' => 'Technical & Creative SEO',
                    'subtitle' => 'A Balanced Approach',
                    'description' => 'We combine technical excellence with creative content strategies to ensure your site is loved by both users and search engines.',
                ],
                'benefits' => [
                    [
                        'title' => 'Higher Visibility',
                        'points' => ['Top 10 Rankings', 'Increased Organic Traffic', 'Brand Awareness'],
                    ],
                    [
                        'title' => 'ROI Tracking',
                        'points' => ['Conversion Monitoring', 'Keyword ROI Analysis'],
                    ],
                ],
                'timeline' => [
                    [
                        'title' => 'Technical Foundation',
                        'duration' => 'Week 1-2',
                        'price' => '$1,500',
                        'items' => ['Technical SEO Audit & Fixes', 'Site Speed Optimization', 'Mobile Responsiveness Improvement', 'Core Web Vitals Optimization'],
                    ],
                    [
                        'title' => 'Content Strategy',
                        'duration' => 'Week 3-6',
                        'price' => '$2,500',
                        'items' => ['Keyword Research & Mapping', 'Content Gap Analysis', 'On-Page Optimization (20 Pages)', 'Meta Tags & Schema Markup'],
                    ],
                    [
                        'title' => 'Growth & Authority',
                        'duration' => 'Week 7-12',
                        'price' => '$3,000/Mo',
                        'items' => ['Link Building Campaign', 'Local SEO Optimization', 'Monthly Performance Reports', 'Ongoing AI Recommendations'],
                    ],
                ],
                'expect_results' => [
                    ['title' => 'Organic Traffic', 'value' => '+150%', 'subtitle' => '6 Months'],
                    ['title' => 'Keyword Rankings', 'value' => 'Top 10', 'subtitle' => '50+ Keywords'],
                    ['title' => 'Domain Authority', 'value' => '+15', 'subtitle' => '12 Months'],
                    ['title' => 'Lead Generation', 'value' => '+200%', 'subtitle' => 'Estimated'],
                ],
            ],
            [
                'title' => 'Local SEO',
                'subtitle' => 'Dominate your local market and attract customers in your area.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'status' => true,
                'pricing_plans' => [
                    [
                        'name' => 'Local Starter',
                        'price' => '299',
                        'features' => ['GMB Optimization', '5 Local Citations', 'Review Management'],
                    ],
                    [
                        'name' => 'Local Dominator',
                        'price' => '599',
                        'features' => ['Map Pack Ranking', '20 Local Citations', 'Local Content Strategy'],
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'What is GMB?',
                        'answer' => 'GMB stands for Google My Business, now called Google Business Profile. It is crucial for local rankings.',
                    ],
                ],
                'process_steps' => [
                    ['title' => 'Cleanup', 'description' => 'Standardizing NAP (Name, Address, Phone) across the web.'],
                    ['title' => 'GMB Setup', 'description' => 'Optimizing your Google Business Profile.'],
                    ['title' => 'Citation Building', 'description' => 'Getting you listed in local directories.'],
                ],
                'section_one' => [
                    'title' => 'Be Found Locally',
                    'subtitle' => 'Near Me Searches',
                    'description' => 'When people search for services "near me", we make sure your business is the one they see first.',
                ],
                'section_two' => [
                    'title' => 'Reviews & Reputation',
                    'subtitle' => 'Build Local Trust',
                    'description' => 'We help you manage local reviews to build a reputation that converts local searchers into customers.',
                ],
                'benefits' => [
                    [
                        'title' => 'Foot Traffic',
                        'points' => ['More store visits', 'Local phone calls'],
                    ],
                    [
                        'title' => 'Map Dominance',
                        'points' => ['Top 3 Map Pack', 'Local Keyword Authority'],
                    ],
                ],
                'timeline' => [
                    [
                        'title' => 'Profile Optimization',
                        'duration' => 'Week 1-2',
                        'price' => '$299',
                        'items' => ['Google Business Profile Setup', 'NAP Consistency Check', 'Local Keyword Research'],
                    ],
                    [
                        'title' => 'Citation Building',
                        'duration' => 'Week 3-4',
                        'price' => '$400',
                        'items' => ['Top 50 Directory Submissions', 'Local Data Aggregator Submission'],
                    ],
                ],
                'expect_results' => [
                    ['title' => 'Local Visibility', 'value' => '+80%', 'subtitle' => '3 Months'],
                    ['title' => 'Calls/Directions', 'value' => '+40%', 'subtitle' => 'Monthly'],
                ],
            ],
            [
                'title' => 'PPC Campaigns',
                'subtitle' => 'Instant traffic and sales through targeted advertising.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'status' => true,
                'pricing_plans' => [
                    [
                        'name' => 'Search Ads',
                        'price' => '399',
                        'features' => ['Google Ads Setup', 'Keyword Research', 'Ad Copywriting'],
                    ],
                    [
                        'name' => 'Full Funnel PPC',
                        'price' => '999',
                        'features' => ['Remarketing', 'Display Ads', 'Strategy Optimization'],
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'How much ad spend do I need?',
                        'answer' => 'We recommend a minimum ad spend of $500/month, but this varies by industry.',
                    ],
                ],
                'process_steps' => [
                    ['title' => 'Targeting', 'description' => 'Identifying your ideal customer profile.'],
                    ['title' => 'Creative', 'description' => 'Designing ads that get clicks.'],
                    ['title' => 'Scaling', 'description' => 'Increasing budget for winning campaigns.'],
                ],
                'section_one' => [
                    'title' => 'Instant ROI',
                    'subtitle' => 'Pay Per Click',
                    'description' => 'Skip the wait of organic growth. Get your message in front of people ready to buy right now.',
                ],
                'section_two' => [
                    'title' => 'Data-Driven Ads',
                    'subtitle' => 'Precision Targeting',
                    'description' => 'We use advanced analytics to ensure every dollar you spend on ads is optimized for maximum return.',
                ],
                'benefits' => [
                    [
                        'title' => 'Immediate Traffic',
                        'points' => ['Go live in 24 hours', 'Instant visibility'],
                    ],
                    [
                        'title' => 'Scalability',
                        'points' => ['Easy budget control', 'Rapid growth potential'],
                    ],
                ],
                'timeline' => [
                    [
                        'title' => 'Campaign Setup',
                        'duration' => 'Week 1',
                        'price' => '$500',
                        'items' => ['Account Structure', 'Conversion Tracking Setup', 'Ad Copy Creation'],
                    ],
                    [
                        'title' => 'Optimization Phase',
                        'duration' => 'Ongoing',
                        'price' => '$250/Mo',
                        'items' => ['Bid Adjustments', 'Negative Keyword Lists', 'A/B Testing Ads'],
                    ],
                ],
                'expect_results' => [
                    ['title' => 'Click Through Rate', 'value' => '3%+', 'subtitle' => 'Industry Avg'],
                    ['title' => 'Conversion Rate', 'value' => '5%+', 'subtitle' => 'Target'],
                ],
            ],
            [
                'title' => 'Content Writing',
                'subtitle' => 'High-quality, SEO-optimized content that resonates with your audience.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'status' => true,
                'pricing_plans' => [
                    [
                        'name' => 'Blog Pack',
                        'price' => '199',
                        'features' => ['4 Blog Posts', 'Keyword Integration', 'Grammarly Checked'],
                    ],
                    [
                        'name' => 'Premium Content',
                        'price' => '499',
                        'features' => ['10 Articles', 'Plagiarism Report', 'Formatting included'],
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'Who writes the content?',
                        'answer' => 'Our team of experienced subjective experts write all content. No AI placeholders.',
                    ],
                ],
                'process_steps' => [
                    ['title' => 'Topics', 'description' => 'Generating ideas that interest your audience.'],
                    ['title' => 'Writing', 'description' => 'Crafting well-researched, engaging articles.'],
                    ['title' => 'Revision', 'description' => 'Polishing the content to perfection.'],
                ],
                'section_one' => [
                    'title' => 'Tell Your Story',
                    'subtitle' => 'Authority Content',
                    'description' => 'Establish yourself as a thought leader in your industry through high-quality, long-form content.',
                ],
                'section_two' => [
                    'title' => 'SEO Optimized',
                    'subtitle' => 'Write for Humans & Bots',
                    'description' => 'Our writers understand SEO. We weave keywords naturally into content to help you rank.',
                ],
                'benefits' => [
                    [
                        'title' => 'Engagement',
                        'points' => ['Lower Bounce Rate', 'Social Shares'],
                    ],
                    [
                        'title' => 'Backlink Magnet',
                        'points' => ['Helpful content focus', 'Resource page worthiness'],
                    ],
                ],
                'timeline' => [
                    [
                        'title' => 'Topic Research & Strategy',
                        'duration' => 'Day 1-3',
                        'price' => '$100',
                        'items' => ['Competitor Content Analysis', 'Keyword Selection', 'Editorial Calendar'],
                    ],
                    [
                        'title' => 'Content Production',
                        'duration' => 'Day 4-10',
                        'price' => '$400',
                        'items' => ['Drafting', 'Editing', 'SEO Formatting'],
                    ],
                ],
                'expect_results' => [
                    ['title' => 'Avg Read Time', 'value' => '3m+', 'subtitle' => 'Engagement'],
                    ['title' => 'New Visitors', 'value' => '+20%', 'subtitle' => 'Monthly Est.'],
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
