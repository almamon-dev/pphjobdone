<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Service;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        $monthlySeo = Service::where('title', 'like', '%Monthly SEO%')->first();
        $contentWriting = Service::where('title', 'like', '%Content Writing%')->first();

        $priceTiers = [
            5 => [
                ['title' => "20 Web 2.0 Blogs", 'sub_items' => ["Indexer #2 (Very High Indexer Rate)"]],
                ['title' => "10 DA 50+ Backlinks", 'sub_items' => ["Indexer #2 (Very High Indexer Rate)"]],
                ['title' => "2030 Mix Profiles Backlinks", 'sub_items' => ["Indexer #1 (95%+ Crawled Rate)"]],
                ['title' => "Tier Project for 1, 2"],
            ],
            10 => [
                ['title' => "30 Web 2.0 Blogs", 'sub_items' => ["Indexer #2 (Very High Indexer Rate)"]],
                ['title' => "20 DA 50+ Backlinks", 'sub_items' => ["Indexer #2 (Very High Indexer Rate)"]],
                ['title' => "30 DA 30+ Backlinks", 'sub_items' => ["Indexer #2 (Very High Indexer Rate)"]],
                ['title' => "2150 Mix Profiles Backlinks", 'sub_items' => ["Indexer #1 (95%+ Crawled Rate)"]],
                ['title' => "Tier Project for 2, 3"],
            ],
            25 => [
                ['title' => "1 The Full Monty Premium Edition", 'sub_items' => ["High DA Sites List", "Indexer #2 for all links"]],
                ['title' => "5 Web 2.0 Blogs Premium", 'sub_items' => ["Human-Quality Content", "Indexer #3 (Maximum Indexer Rate)"]],
                ['title' => "25 DA 30+ Backlinks", 'sub_items' => ["Indexer #2 (Very High Indexer Rate)"]],
                ['title' => "10965 Mix Profiles Backlinks", 'sub_items' => ["Indexer #1 (95%+ Crawled Rate)"]],
                ['title' => "Tier Project for 2, 3"],
            ],
            50 => [
                ['title' => "1 The Full Monty Premium Edition", 'sub_items' => ["High DA Sites List", "Indexer #2 for all links"]],
                ['title' => "10 Web 2.0 Blogs Premium", 'sub_items' => ["Human-Quality Content", "Indexer #3 (Maximum Indexer Rate)"]],
                ['title' => "25 DA 50+ Backlinks", 'sub_items' => ["Indexer #2 (Very High Indexer Rate)"]],
                ['title' => "100 DA 30+ Backlinks", 'sub_items' => ["Indexer #2 (Very High Indexer Rate)"]],
                ['title' => "9900 Mix Platforms Backlinks", 'sub_items' => ["Indexer #1 (95%+ Crawled Rate)"]],
                ['title' => "5000 Mix Profiles Backlinks", 'sub_items' => ["Indexer #1 (95%+ Crawled Rate)"]],
                ['title' => "Tier Project for 2, 3, 4"],
            ],
            75 => [
                ['title' => "1 The Full Monty Premium Edition", 'sub_items' => ["High DA Sites List", "Indexer #2 for all links"]],
                ['title' => "20 Web 2.0 Blogs Premium", 'sub_items' => ["Human-Quality Content", "Indexer #3 (Maximum Indexer Rate)"]],
                ['title' => "25 DA 50+ Backlinks", 'sub_items' => ["Indexer #2 (Very High Indexer Rate)"]],
                ['title' => "100 DA 30+ Backlinks", 'sub_items' => ["Indexer #2 (Very High Indexer Rate)"]],
                ['title' => "9870 Mix Platforms Backlinks", 'sub_items' => ["Indexer #1 (95%+ Crawled Rate)"]],
                ['title' => "5000 Mix Profiles Backlinks", 'sub_items' => ["Indexer #1 (95%+ Crawled Rate)"]],
                ['title' => "Tier Project for 2, 3, 4"],
            ],
            100 => [
                ['title' => "1 The Full Monty Premium Edition", 'sub_items' => ["High DA Sites List", "Indexer #2 for all links"]],
                ['title' => "20 Web 2.0 Blogs Premium", 'sub_items' => ["Human-Quality Content", "500 Visits each", "100 Social Signals each", "Indexer #3 (Max Rate)"]],
                ['title' => "25 DA 50+ Backlinks", 'sub_items' => ["Indexer #2 (Very High Indexer Rate)"]],
                ['title' => "100 DA 30+ Backlinks", 'sub_items' => ["Indexer #2 (Very High Indexer Rate)"]],
                ['title' => "13200 Mix Platforms Backlinks", 'sub_items' => ["Indexer #1 (95%+ Crawled Rate)"]],
                ['title' => "5000 Mix Profiles Backlinks", 'sub_items' => ["Indexer #1 (95%+ Crawled Rate)"]],
                ['title' => "Tier Project for 2, 3, 4"],
            ],
            150 => [
                ['title' => "1 The Full Monty Premium Edition", 'sub_items' => ["Human-Quality Content (25 articles)", "Indexer #2 for all links"]],
                ['title' => "25 Web 2.0 Blogs Premium", 'sub_items' => ["Human-Quality Content", "500 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "25 DA 50+ Do-follow Backlinks", 'sub_items' => ["Human-Quality Content (1 art/2 links)", "Indexer #2 (Very High Rate)"]],
                ['title' => "170 DA 30+ Backlinks", 'sub_items' => ["Indexer #2 (Very High Indexer Rate)"]],
                ['title' => "14655 Mix Platforms Backlinks", 'sub_items' => ["Indexer #1 (95%+ Crawled Rate)"]],
                ['title' => "10000 Mix Profiles Backlinks", 'sub_items' => ["Indexer #1 (95%+ Crawled Rate)"]],
                ['title' => "Tier Project for 2, 3, 4"],
            ],
            200 => [
                ['title' => "1 The Full Monty Premium Edition", 'sub_items' => ["Human-Quality Content (25 articles)", "Indexer #3 (Max Rate)"]],
                ['title' => "25 Web 2.0 Blogs Premium", 'sub_items' => ["Human-Quality Content", "Custom Image Design", "500 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "27 DA 50+ Backlinks", 'sub_items' => ["Human-Quality Content (1 art/2 links)", "Indexer #3 (Max Rate)"]],
                ['title' => "100 DA 30+ Backlinks", 'sub_items' => ["Human-Quality Content (1 art/4 links)", "Indexer #3 (Max Rate)"]],
                ['title' => "20034 Mix Platforms Backlinks", 'sub_items' => ["Indexer #1 (95%+ Crawled Rate)"]],
                ['title' => "10000 Mix Profiles Backlinks", 'sub_items' => ["Indexer #1 (95%+ Crawled Rate)"]],
                ['title' => "Tier Project for 2, 3, 4"],
            ],
            250 => [
                ['title' => "1 The Full Monty Premium Edition", 'sub_items' => ["Human-Quality Content (25 articles)", "Indexer #3 (Max Rate)"]],
                ['title' => "25 Web 2.0 Blogs Premium", 'sub_items' => ["Human-Quality Content", "Custom Image Design", "500 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "30 DA 50+ Backlinks", 'sub_items' => ["1 Article per link", "Indexer #3 (Max Rate)"]],
                ['title' => "100 DA 30+ Backlinks", 'sub_items' => ["1 art/4 links", "Indexer #3 (Max Rate)"]],
                ['title' => "22115 URL Shortener Backlinks", 'sub_items' => ["Indexer #2 (Very High Rate)"]],
                ['title' => "15000 Mix Profiles Backlinks", 'sub_items' => ["Indexer #2 (Very High Rate)"]],
                ['title' => "Tier Project for 2, 3, 4"],
            ],
            500 => [
                ['title' => "1 The Full Monty Premium Edition", 'sub_items' => ["Human-Quality Content (100 articles)", "500 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "30 Web 2.0 Blogs Premium", 'sub_items' => ["Human-Quality Content", "Custom Image Design", "1000 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "50 DA 50+ Backlinks", 'sub_items' => ["1 Article per link", "500 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "100 DA 30+ Backlinks", 'sub_items' => ["1 art/2 links", "500 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "32967 Mix Platforms Backlinks", 'sub_items' => ["Indexer #2 (Very High Rate)"]],
                ['title' => "20000 Mix Profiles Backlinks", 'sub_items' => ["Indexer #2 (Very High Rate)"]],
                ['title' => "Tier Project for 2, 3, 4"],
            ],
            750 => [
                ['title' => "1 The Full Monty Premium Edition", 'sub_items' => ["Human-Quality Content (200 articles)", "500 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "40 Web 2.0 Blogs Premium", 'sub_items' => ["Human-Quality Content", "Custom Image Design", "1000 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "60 PR9 - DA 70+ Backlinks", 'sub_items' => ["1 Article per link", "500 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "100 DA 50+ Backlinks", 'sub_items' => ["1 Article per link", "500 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "100 DA 30+ Backlinks", 'sub_items' => ["1 art/2 links", "500 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "27654 Mix Platforms Backlinks", 'sub_items' => ["Indexer #2 (Very High Rate)"]],
                ['title' => "19000 Mix Profiles Backlinks", 'sub_items' => ["Indexer #2 (Very High Rate)"]],
                ['title' => "Tier Project for 2, 3, 4, 5"],
            ],
            1000 => [
                ['title' => "1 The Full Monty Premium Edition", 'sub_items' => ["Human-Quality Content (200 articles)", "500 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "55 Web 2.0 Blogs Premium", 'sub_items' => ["Human-Quality Content", "Custom Image Design", "1000 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "60 PR9 - DA 70+ Backlinks", 'sub_items' => ["1 Article per link", "1000 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "100 DA 50+ Backlinks", 'sub_items' => ["1 Article per link", "1000 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "100 DA 30+ Backlinks", 'sub_items' => ["1 Article per link", "1000 Visits each", "Indexer #3 (Max Rate)"]],
                ['title' => "69564 Mix Platforms Backlinks", 'sub_items' => ["Indexer #2 (Very High Rate)"]],
                ['title' => "49900 Mix Profiles Backlinks", 'sub_items' => ["Indexer #2 (Very High Rate)"]],
                        ['title' => "Tier Project for 2, 3, 4, 5"],
            ]
        ];

        // 1. Submit SEO Campaign (Under Monthly SEO Service)
        if ($monthlySeo) {
            $campaign = Campaign::updateOrCreate(
                ['service_id' => $monthlySeo->id, 'title' => 'Submit SEO Campaign'],
                ['subtitle' => 'Choose a tier, enter your details, and checkout securely.', 'status' => true]
            );

            // Ensure we have exactly the price tiers requested
            $campaign->tiers()->delete();
            foreach ($priceTiers as $price => $features) {
                $tier = $campaign->tiers()->create(['price' => $price, 'status' => true]);
                foreach ($features as $f) {
                    if (is_array($f)) {
                        $tier->features()->create([
                            'feature_text' => $f['title'],
                            'sub_items' => $f['sub_items'] ?? [],
                        ]);
                    } else {
                        $tier->features()->create(['feature_text' => $f]);
                    }
                }
            }
        }

        // 2. Add Content Writing Campaigns
        if ($contentWriting) {
            // New Marketing Campaigns
            $marketingCampaigns = [
                [
                    'title' => 'Submit Instagram Marketing Campaign',
                    'subtitle' => 'Choose a tier, enter your account details and goals, then checkout securely.',
                    'tiers' => [
                        2 => [['title' => 'Quick Instagram account audit', 'sub_items' => ['Basic hashtag research']]],
                        10 => [['title' => 'Audit + setup of 1 boosted Story OR Feed ad', 'sub_items' => ['Basic targeting (country/interest)', 'Delivery within 7 days']]],
                        25 => [['title' => 'Audit + setup of 2 ads (Story/Feed)', 'sub_items' => ['Simple audience targeting', 'Basic performance snapshot']]],
                        50 => [['title' => 'Audit + setup of 3 ads', 'sub_items' => ['Audience targeting refinement', 'Weekly performance snapshot']]],
                        100 => [['title' => 'Audit + setup of 5 ads', 'sub_items' => ['1 Story ad included', 'Monthly performance summary']]],
                        150 => [['title' => 'Audit + setup of 8 ads', 'sub_items' => ['1 Story ad included', 'Hashtag strategy (10–15 tags)', 'Monthly performance summary']]],
                        250 => [['title' => 'Audit + setup of 12 ads', 'sub_items' => ['2 Story/Carousel ads included', 'Basic content suggestions', 'Monthly performance report']]],
                        350 => [['title' => 'Audit + setup of 18 ads', 'sub_items' => ['2 Story/Carousel ads', '1-week content calendar', 'Bi-weekly reporting']]],
                        450 => [['title' => 'Audit + setup of 25 ads', 'sub_items' => ['3 Story/Carousel ads', 'Content calendar (1–2 weeks)', 'Growth suggestions + Bi-weekly report']]],
                        600 => [['title' => '30-day Instagram campaign (max 30 days)', 'sub_items' => ['Full account audit & optimisation', 'Campaign management + ad promotion', 'Interaction/engagement boost (targeted)', 'Weekly reporting & strategy check-in', 'Target audience research + hashtag plan']]],
                    ]
                ],
                [
                    'title' => 'Submit Facebook Marketing Campaign',
                    'subtitle' => 'Choose a plan, enter your Page and goals, then checkout securely.',
                    'tiers' => [
                        2 => [['title' => 'Quick Facebook Page review', 'sub_items' => ['Basic interest list (draft)']]],
                        10 => [['title' => 'Audit + setup of 1 boosted Post ad', 'sub_items' => ['Basic targeting (country/interest)', 'Delivery within 7 days']]],
                        25 => [['title' => 'Audit + setup of 2 ads (Feed/Right column)', 'sub_items' => ['Simple audience targeting', 'Basic performance snapshot']]],
                        50 => [['title' => 'Audit + setup of 3 ads', 'sub_items' => ['Audience targeting refinement', 'Weekly performance snapshot']]],
                        100 => [['title' => 'Audit + setup of 5 ads', 'sub_items' => ['Pixel/domain check (basic)', 'Monthly performance summary']]],
                        150 => [['title' => 'Audit + setup of 8 ads', 'sub_items' => ['Pixel & conversion event review', 'Monthly performance summary']]],
                        250 => [['title' => 'Audit + setup of 12 ads', 'sub_items' => ['2 Carousel/Video ads included', 'Basic creative suggestions', 'Monthly performance report']]],
                        350 => [['title' => 'Audit + setup of 18 ads', 'sub_items' => ['2 Carousel/Video ads', '1-week content calendar', 'Bi-weekly reporting']]],
                        450 => [['title' => 'Audit + setup of 25 ads', 'sub_items' => ['3 Carousel/Video ads', 'Content calendar (1–2 weeks)', 'Growth suggestions + Bi-weekly report']]],
                        600 => [['title' => '30-day Facebook campaign (max 30 days)', 'sub_items' => ['Full page/account audit & optimisation', 'Campaign management + ad optimisation', 'Interaction moderation (basic messages/comments)', 'Weekly reporting & strategy check-in', 'Target audience research + pixel/Conversions API check']]],
                    ]
                ],
                [
                    'title' => 'Submit YouTube Marketing Campaign',
                    'subtitle' => 'Choose a plan, enter your channel and goals, then checkout securely.',
                    'tiers' => [
                        2 => [['title' => 'Quick channel review', 'sub_items' => ['Basic keyword ideas (draft)']]],
                        10 => [['title' => 'Audit + setup of 1 boosted in-feed or in-stream ad', 'sub_items' => ['Basic geo/interest targeting', 'Delivery within 7 days']]],
                        25 => [['title' => 'Audit + setup of 2 ads (in-feed/in-stream)', 'sub_items' => ['Simple audience targeting', 'Basic performance snapshot']]],
                        50 => [['title' => 'Audit + setup of 3 ads', 'sub_items' => ['Audience targeting refinement', 'Weekly performance snapshot']]],
                        100 => [['title' => 'Audit + setup of 5 ads', 'sub_items' => ['Basic conversion tracking check (where applicable)', 'Monthly performance summary']]],
                        150 => [['title' => 'Audit + setup of 8 ads', 'sub_items' => ['Ad creative suggestions (titles/thumbnails)', 'Monthly performance summary']]],
                        250 => [['title' => 'Audit + setup of 12 ads', 'sub_items' => ['Skippable/Non-skippable mix (where eligible)', 'Basic content suggestions', 'Monthly performance report']]],
                        350 => [['title' => 'Audit + setup of 18 ads', 'sub_items' => ['2 bumper or non-skippable inclusions (if approved)', '1-week content calendar (topics/titles)', 'Bi-weekly reporting']]],
                        450 => [['title' => 'Audit + setup of 25 ads', 'sub_items' => ['3 bumper/non-skippable inclusions (if approved)', 'Content calendar (1–2 weeks) + growth suggestions', 'Bi-weekly reporting']]],
                        600 => [['title' => '30-day YouTube campaign (max 30 days)', 'sub_items' => ['Full channel audit & optimisation (metadata, thumbnails, playlists)', 'Campaign management + ad optimisation', 'Audience targeting & keyword plan', 'Weekly reporting & strategy check-in', 'Goal-focused setup (views, engagement, traffic, or leads)']]],
                    ]
                ]
            ];

            foreach ($marketingCampaigns as $mCampaign) {
                $campaign = Campaign::updateOrCreate(
                    ['service_id' => $contentWriting->id, 'title' => $mCampaign['title']],
                    ['subtitle' => $mCampaign['subtitle'], 'status' => true]
                );

                // Clean old tiers to ensure we have exactly what the user requested
                $campaign->tiers()->delete();

                foreach ($mCampaign['tiers'] as $price => $features) {
                    $tier = $campaign->tiers()->create(['price' => $price, 'status' => true]);
                    foreach ($features as $f) {
                        $tier->features()->create([
                            'feature_text' => $f['title'],
                            'sub_items' => $f['sub_items'] ?? []
                        ]);
                    }
                }
            }
        }
    }
}
