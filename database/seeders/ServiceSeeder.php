<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
                'slug' => 'monthly-seo',
                'subtitle' => 'Drive Organic Traffic and Sales',
                'description' => 'Comprehensive monthly SEO strategy to grow your business.',
                'features' => ['Keyword Research', 'On-page SEO', 'Backlink Building'],
                'pricing_plans' => [
                    ['name' => 'Starter', 'price' => '$500', 'features' => ['Basic Keywords', 'Monthly Report']],
                    ['name' => 'Growth', 'price' => '$1000', 'features' => ['Advanced Keywords', 'Backlinks']],
                    ['name' => 'Premium', 'price' => '$2000', 'features' => ['Enterprise SEO', 'Dedicated Manager']],
                ],
                'status' => true,
            ],
            [
                'title' => 'Local SEO',
                'slug' => 'local-seo',
                'subtitle' => 'Dominating Local Search',
                'description' => 'Get found by local customers in your neighborhood.',
                'features' => ['Google My Business', 'Local Citations', 'Review Management'],
                'pricing_plans' => [
                    ['name' => 'Local Starter', 'price' => '$300', 'features' => ['GMB Optimization', '10 Citations']],
                    ['name' => 'Local Growth', 'price' => '$600', 'features' => ['GMB Posts', '30 Citations']],
                ],
                'status' => true,
            ],
            [
                'title' => 'PPC Management',
                'slug' => 'ppc-management',
                'subtitle' => 'Profitable Paid Campaigns',
                'description' => 'Maximize your ROI with expert Google Ads and Social Ads management.',
                'features' => ['Ad Copywriting', 'Bid Management', 'A/B Testing'],
                'pricing_plans' => [
                    ['name' => 'Basic PPC', 'price' => '$400', 'features' => ['1 Platform', 'Weekly Updates']],
                    ['name' => 'Advanced PPC', 'price' => '$800', 'features' => ['Multi-platform', 'Daily Monitoring']],
                ],
                'status' => true,
            ],
            [
                'title' => 'Content Writing',
                'slug' => 'content-writing',
                'subtitle' => 'SEO Content that Converts',
                'description' => 'High-quality, engaging content tailored for your audience and search engines.',
                'features' => ['Blog Posts', 'Product Descriptions', 'Sales Copy'],
                'pricing_plans' => [
                    ['name' => 'Starter Pack', 'price' => '$200', 'features' => ['4 Blog Posts', 'SEO Optimized']],
                    ['name' => 'Power Pack', 'price' => '$500', 'features' => ['10 Blog Posts', 'Content Strategy']],
                ],
                'status' => true,
            ],
        ];

        foreach ($services as $service) {
            \App\Models\Service::create($service);
        }
    }
}
