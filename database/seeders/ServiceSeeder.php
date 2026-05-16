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

        $services = [
            [
                'title' => 'Monthly SEO',
                'subtitle' => 'Comprehensive monthly SEO campaigns for maximum growth',
                'description' => 'Scale your business with our tailored monthly SEO strategies. We provide consistent growth through technical optimization, content, and high-quality links.',
                'status' => true,
                'faqs' => [
                    ['question' => 'How long until I see results?', 'answer' => 'SEO is a long-term game. Typically, you will see noticeable improvements within 3-6 months.'],
                    ['question' => 'Do you provide reports?', 'answer' => 'Yes, we provide detailed monthly reports on rankings, traffic, and work completed.'],
                ],
                'process_steps' => [
                    ['title' => 'Audit & Strategy', 'description' => 'We start with a deep dive into your current site performance and competition.'],
                    ['title' => 'Optimization', 'description' => 'Fixing technical issues and optimizing on-page elements.'],
                    ['title' => 'Link Building', 'description' => 'Earning high-quality backlinks to boost your authority.'],
                ],
                'section_one' => [
                    'title' => 'Long-Term Growth Engine',
                    'subtitle' => 'Sustainable SEO',
                    'description' => 'Our monthly SEO plans are designed for businesses that want consistent, long-term organic growth.',
                ],
                'section_two' => [
                    'title' => 'Data-Driven Strategy',
                    'subtitle' => 'Expert Insights',
                    'description' => 'We use enterprise-grade tools and years of experience to drive your rankings higher.',
                ],
                'benefits' => [
                    [
                        'title' => 'Organic Growth',
                        'points' => ['Higher Rankings', 'More Traffic', 'Better Brand Visibility'],
                    ],
                ],
                'expect_results' => [
                    ['title' => 'Traffic Increase', 'value' => '+150%', 'subtitle' => 'Organic Reach'],
                ],
            ],
            [
                'title' => 'Local SEO',
                'subtitle' => 'Dominate your local market and the Pack 3.',
                'description' => 'Own the "near me" searches. We optimize your local ecosystem—from Google Business Profile to localized landing pages—to drive high-intent store visits and calls.',
                'status' => true,
                'faqs' => [
                    ['question' => 'What is the Pack 3?', 'answer' => 'The top three local results shown on Google Maps above organic results.'],
                ],
                'process_steps' => [
                    ['title' => 'GBP Optimization', 'description' => 'Professional audit and enrichment of your Google Business Profile.'],
                    ['title' => 'Localized Citations', 'description' => 'Consistency management across 60+ high-authority geo-directories.'],
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
                'expect_results' => [
                    ['title' => 'Map Reach', 'value' => 'Top 3', 'subtitle' => 'Consistent Pack'],
                ],
            ],
            [
                'title' => 'PPC Campaigns',
                'subtitle' => 'Surgical precision for instant, scalable ROI.',
                'description' => 'Stop burning money on ads. We manage Google Ads and Meta campaigns with a ruthless focus on conversions, optimizing every cent of your ad spend.',
                'status' => true,
                'faqs' => [
                    ['question' => 'How much should I spend?', 'answer' => 'We recommend a starting ad spend of at least $1,500/mo for best results.'],
                ],
                'process_steps' => [
                    ['title' => 'Funnel Architecture', 'description' => 'Building non-leaky tracking systems and high-converting landing pages.'],
                    ['title' => 'Algorithmic Bidding', 'description' => 'Leveraging machine learning for real-time bid adjustments.'],
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
                'expect_results' => [
                    ['title' => 'ROAS', 'value' => '400%+', 'subtitle' => 'Target Growth'],
                ],
            ],
            [
                'title' => 'Content Writing',
                'subtitle' => 'Topical authority that ranks and converts.',
                'description' => 'We don’t just write blogs; we build topical dominance. Every piece is engineered with TF-IDF optimization and search intent mapping.',
                'status' => true,
                'faqs' => [
                    ['question' => 'Who writes the content?', 'answer' => 'Subject matter experts with a deep understanding of SEO copywriting.'],
                ],
                'process_steps' => [
                    ['title' => 'Topic Cluster Mapping', 'description' => 'Building internal link power through semantic keyword grouping.'],
                    ['title' => 'Intent Optimization', 'description' => 'Drafting content that aligns with the buyer journey.'],
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
                'expect_results' => [
                    ['title' => 'Keyword Coverage', 'value' => '+400%', 'subtitle' => 'Visibility Scope'],
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            Service::updateOrCreate(
                ['slug' => Str::slug($serviceData['title'])],
                $serviceData
            );
        }
    }
}
