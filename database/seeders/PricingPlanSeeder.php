<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PricingPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter SEO',
                'price' => '499',
                'subtitle' => 'Perfect for small businesses starting their journey.',
                'is_popular' => false,
                'features' => ['5 Keywords', 'On-Page Optimization', 'Monthly Report'],
                'button_text' => 'Get Started',
                'button_url' => '#',
            ],
            [
                'name' => 'Growth SEO',
                'price' => '899',
                'subtitle' => 'For growing businesses seeking higher rankings.',
                'is_popular' => true,
                'features' => ['15 Keywords', 'Content Creation', 'Backlink Building', 'Quarterly Strategy'],
                'button_text' => 'Get Started',
                'button_url' => '#',
            ],
            [
                'name' => 'Enterprise SEO',
                'price' => '1499',
                'subtitle' => 'Comprehensive solutions for large scale dominance.',
                'is_popular' => false,
                'features' => ['Unlimited Keywords', 'Technical Audit', 'Competitor Analysis', 'Dedicated Manager'],
                'button_text' => 'Get Started',
                'button_url' => '#',
            ],
        ];

        foreach ($plans as $plan) {
            \App\Models\PricingPlan::create($plan);
        }
    }
}
