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
                'title' => 'Monthly SEO That Grows Revenue',
                'slug' => 'monthly-seo',
                'thumbnail' => 'images/seo-thumbnail.jpg',
                'subtitle' => 'Comprehensive monthly SEO campaigns for maximum growth',
                'description' => 'Scale your business with our tailored monthly SEO strategies. We provide consistent growth through technical optimization, content, and high-quality links.',
                'status' => true,
                'is_campaign' => true,
                'has_faq' => true,
                'has_brands' => false,
                'has_expect_result' => false,
                'has_benifite' => true,
                'has_why_chose_us' => true,
                'has_secondary_features' => false,
                'faqs' => [
                    ['question' => 'How long until I see results?', 'answer' => 'SEO is a long-term game. Typically, you will see noticeable improvements within 3-6 months.'],
                    ['question' => 'Do you provide reports?', 'answer' => 'Yes, we provide detailed monthly reports on rankings, traffic, and work completed.'],
                ],
                'section_one' => [
                    'title' => 'Witness A Surge In Your Website Traffic!',
                    'subtitle' => 'Our Benefit',
                    'description' => "We take immense pride in using our expertise in SEO to take your website to heights that you couldn't have envisioned. We work on",
                    'points' => [
                        'A Fully-Packed SEO Campaign',
                        'Link-Building',
                        'Marketing'
                    ],
                    'button_text' => 'Contact Us',
                    'image' => 'images/seo-benefits.jpg',
                ],
                'section_two' => [
                    'title' => 'Why GAJURA SEO Stands Out',
                    'subtitle' => 'Why Choose Us',
                    'description' => "Because we have you could ask for and more\n\nWe Don't Leave Anything Out. We Realize That SEO Is A Pretty Broad Concept, Encompasses A Variety Of Factors. And We Work On Everything To Benefit Your Website In The Best Possible Way.",
                    'points' => [
                        "Copywriting: One Of Our Most Popular Services Is Winning The Visitor's Heart With Our WordsZ",
                        "Traffic Analysis: We Work To Improve Your Content Based On The"
                    ],
                    'button_text' => 'Contact Us',
                    'image' => 'images/seo-why-us.jpg',
                ],
                'service_features' => [
                    [
                        'title' => 'Competitor Analysis',
                        'description' => "It's vital to keep tabs on what the competitor is up to. How else will you be able to prepare your strategy? We will not only work on your website but also find out what makes your competitors' websites click with the audience.",
                        'icon' => 'icons/megaphone.svg',
                    ],
                    [
                        'title' => 'Website Audit',
                        'description' => 'Get every aspect of your website scrutinized and analyzed to figure out your strengths and weaknesses. This will enable you to take measures timely and make a difference!',
                        'icon' => 'icons/audit.svg',
                    ],
                    [
                        'title' => 'Speed Optimization',
                        'description' => "Your site's responsiveness is a key aspect of user experience. No one has the time to browse through a website that takes ages to load. Let us work on ensuring that your website's speed doesn't leave much to desire.",
                        'icon' => 'icons/speed.svg',
                    ],
                    [
                        'title' => 'Traffic Analysis',
                        'description' => "Figure out what your targeted audience likes. A lot can be revealed by analyzing the behavior of your target audience. An SEO campaign that caters to the target audience's requirements is bound to succeed.",
                        'icon' => 'icons/megaphone.svg',
                    ],
                    [
                        'title' => 'Website Migration',
                        'description' => "Upgrading a website for search engine optimization can be confusing. There's so much to focus on, including the site's location, structure, design, content, and so much more. Trust us to ensure that nothing's left out!",
                        'icon' => 'icons/audit.svg',
                    ],
                    [
                        'title' => 'Technical SEO',
                        'description' => 'If you want to improve the organic ranking of your website, you must adhere to the technical requirements of modern search engines. And this is where we can help you.',
                        'icon' => 'icons/speed.svg',
                    ],
                ],
            ],
            [
                'title' => 'Rank Your Business At The Top Of Local Search',
                'slug' => 'local-seo',
                'thumbnail' => 'images/seo-thumbnail.jpg',
                'subtitle' => 'Dominate your local market and the Pack 3.',
                'description' => 'Own the "near me" searches. We optimize your local ecosystem—from Google Business Profile to localized landing pages—to drive high-intent store visits and calls.',
                'status' => true,
                'is_campaign' => false,
                'has_faq' => true,
                'has_secondary_features' => true,
                'has_brands' => false,
                'has_expect_result' => false,
                'has_benifite' => true,
                'has_why_chose_us' => true,
                'faqs' => [
                    [
                        'question' => 'Are local SEO packages different from regular SEO packages?', 
                        'answer' => 'Yes, local SEO packages focus specifically on optimizing your presence for local searches, emphasizing Google Business Profile optimization, local citations, and geo-targeted keywords to attract customers in your specific geographic area.'
                    ],
                    [
                        'question' => 'How much does a local SEO cost?', 
                        'answer' => 'The cost of local SEO varies depending on the competitiveness of your industry and location. Our pricing is transparent and designed to provide the best return on investment for your specific needs.'
                    ],
                    [
                        'question' => 'How to track success in a local SEO campaign?', 
                        'answer' => 'We track success through key metrics such as local search rankings, Google Business Profile insights (calls, directions, website clicks), and overall increase in targeted local organic traffic.'
                    ],
                    [
                        'question' => 'Can I hire someone locally to do local SEO for me?', 
                        'answer' => 'While you can hire locally, partnering with a specialized agency like ours gives you access to a team of experts with proven strategies, advanced tools, and a track record of driving scalable local growth.'
                    ],
                    [
                        'question' => 'What are local SEO services?', 
                        'answer' => 'Local SEO services include optimizing your Google Business Profile, managing local directory citations, creating localized website content, and earning local backlinks to improve your visibility in local search results.'
                    ],
                    [
                        'question' => 'How to do a local SEO audit?', 
                        'answer' => 'A comprehensive local SEO audit involves analyzing your Google Business Profile, checking the consistency of your NAP (Name, Address, Phone) citations across directories, reviewing local rankings, and assessing on-page local SEO factors.'
                    ],
                ],
                'section_one' => [
                    'title' => 'Guest Posting That Makes A Difference!',
                    'subtitle' => 'Our Benefit',
                    'description' => "It's a working practically counting ranking parameters of search engines! What if we provided you with practically the best links! Without compromising!",
                    'points' => [
                        "Link Earning: Our links improve your Search Engine Rankings! No matter what you think! You basically get the best value for your money!",
                        "Traffic: Our links also improve the website ranking of your website, not just your rank! We aim to increase the Domain Authority (DA)!",
                        "Rank Impact: We improve the rankings of your overall website! Without any hidden fees or strings attached! Basically providing what you deserve!"
                    ],
                    'button_text' => 'Contact Us',
                    'image' => 'images/seo-benefits.jpg',
                ],
                'secondary_features' => [
                    [
                        'title' => 'Research',
                        'description' => 'Target Public, Competitors, and Strategy! We dissect it all to craft out the best strategy!',
                        'icon' => 'icons/megaphone.svg',
                    ],
                    [
                        'title' => 'Contextual Links',
                        'description' => 'Contextual links hit the spot quite right! They provide exactly what you need out of a link!',
                        'icon' => 'icons/audit.svg',
                    ],
                    [
                        'title' => 'Content That Works',
                        'description' => 'Our content makes your target audience trust you. It lets them know that they are dealing with the best.',
                        'icon' => 'icons/speed.svg',
                    ],
                ],
                'section_two' => [
                    'title' => 'Enhance Your Website Traffic Now!',
                    'subtitle' => 'Targeted Reach',
                    'description' => "Get professional guidance and complete technical execution that will skyrocket your product's organic value.",
                    'points' => [
                        "Why Choose Us",
                        "Our Benefits",
                        "Our Pricing"
                    ],
                    'button_text' => 'Contact Us',
                    'image' => 'images/seo-why-us.jpg',
                ],
                'service_features' => [
                    [
                        'title' => 'High-Quality Web',
                        'description' => "You probably already got a website created, but a Web Site Without the Best Design is basically a dead site! We help your site rank online.",
                        'icon' => 'icons/audit.svg',
                    ],
                    [
                        'title' => 'Contextual Links',
                        'description' => "We build links strategically and present you with links related to your website! There is no point for a tech site to get a link.",
                        'icon' => 'icons/megaphone.svg',
                    ],
                    [
                        'title' => 'Content That Works',
                        'description' => "We provide content that practically hooks the reader in, increasing the retention rate! You get what you came looking for with us!",
                        'icon' => 'icons/speed.svg',
                    ],
                    [
                        'title' => 'Rapid Turn-Around',
                        'description' => "Time is money! We deliver exactly on time so that you don't face any inconvenience! In fact, most orders are completed ahead of schedule!",
                        'icon' => 'icons/megaphone.svg',
                    ],
                    [
                        'title' => 'Scaled Results',
                        'description' => "Our process is basically scalable! That means you can always order more from us and we'll deliver it!",
                        'icon' => 'icons/audit.svg',
                    ],
                    [
                        'title' => 'Customer Satisfaction',
                        'description' => "We prioritize our clients before anything else! We aim to achieve 100% satisfaction! We guarantee you won't regret your order with us!",
                        'icon' => 'icons/speed.svg',
                    ],
                ],
            ],
            [
                'title' => 'Profitable PPC Campaigns',
                'slug' => 'ppc-campaigns',
                'thumbnail' => 'images/seo-benefits.jpg',
                'subtitle' => 'GROWTH PLAN',
                'description' => "We Offer Link Building Strategies That Nobody Delivers!\nLink building is quite a broad term and requires quite a lot of research to find what strategies are working these days and what's not. What works for others might not work for you! You need to have the right partner on your side if you want to grow!",
                'status' => true,
                'is_campaign' => false,
                'has_faq' => true,
                'has_secondary_features' => true,
                'has_brands' => false,
                'has_expect_result' => false,
                'has_benifite' => true,
                'has_why_chose_us' => true,
                'faqs' => [
                    [
                        'question' => 'Are local SEO packages different from regular SEO packages?', 
                        'answer' => 'Yes, local SEO packages focus specifically on optimizing your presence for local searches, emphasizing Google Business Profile optimization, local citations, and geo-targeted keywords.'
                    ],
                    [
                        'question' => 'How much does a PPC audit cost?', 
                        'answer' => 'The cost of a PPC audit varies depending on the size and complexity of your account. We offer transparent pricing after an initial evaluation of your current campaigns.'
                    ],
                    [
                        'question' => 'Is it possible to increase traffic naturally?', 
                        'answer' => 'Absolutely. Through consistent organic search engine optimization, content marketing, and ethical link building, your organic traffic will naturally increase over time.'
                    ],
                    [
                        'question' => 'Do I need to be involved heavily in your process?', 
                        'answer' => 'We handle the heavy lifting. We will need your initial input regarding business goals and approvals, but our team manages the day-to-day execution and strategy.'
                    ],
                    [
                        'question' => 'What is White Hat SEO?', 
                        'answer' => 'White Hat SEO refers to ethical optimization strategies that strictly follow search engine guidelines, ensuring long-term, sustainable growth without the risk of penalties.'
                    ],
                    [
                        'question' => 'How is PPC different from SEO?', 
                        'answer' => 'PPC provides immediate visibility through paid advertisements where you pay per click, while SEO builds organic visibility over time without direct payment for clicks.'
                    ],
                ],
                'section_one' => [
                    'title' => 'Benefits For Your Business',
                    'subtitle' => 'LINK EARNING, IMPROVES RANKINGS!',
                    'description' => 'What are your benefits from link earning/guest posting? For starters, your website rankings are going to improve!',
                    'points' => [
                        "Content: Link Earning relies heavily on Guest Posting! We make sure that the content that goes along with your link is top tier!",
                        "Improve Search Engine Rankings: Link Earning improves the Domain Authority (DA) of your website which is how you improve your rankings!",
                        "Get Targeted Traffic: Link Earning basically provides you with traffic that actually matters! Traffic that is relevant to your website!"
                    ],
                    'button_text' => 'Get Started',
                    'image' => 'images/seo-benefits.jpg',
                ],
                'secondary_features' => [
                    [
                        'title' => 'Organic Link Building',
                        'description' => "We don't use any bots. Rest assured, your website's rankings will improve safely!",
                        'icon' => 'icons/megaphone.svg',
                    ],
                    [
                        'title' => 'Outreach',
                        'description' => 'We do our outreach manually. We find blogs within your niche and pitch to them!',
                        'icon' => 'icons/audit.svg',
                    ],
                    [
                        'title' => 'Analyzing The Backlinks',
                        'description' => "We analyze all of your links to make sure they are performing well. There shouldn't be any dead links!",
                        'icon' => 'icons/speed.svg',
                    ],
                ],
                'section_two' => [
                    'title' => 'We Use Every Resource For Link Building',
                    'subtitle' => '',
                    'description' => "Link building shouldn't be done using just a single strategy. That's a surefire way to have the opposite effect to what you want to achieve!\n\nFrom local citations to contextual links, we pull out all the stops! Your competitors won't be able to keep up with you!",
                    'points' => [],
                    'button_text' => 'Get Started',
                    'image' => 'images/seo-benefits.jpg',
                ],
                'service_features' => [
                    [
                        'title' => 'Keyword Analysis',
                        'description' => 'We start off with detailed keywords related to your niche!',
                        'icon' => 'icons/audit.svg',
                    ],
                    [
                        'title' => 'Competitor Analysis',
                        'description' => 'Who is your competitor? We find them and target them!',
                        'icon' => 'icons/megaphone.svg',
                    ],
                    [
                        'title' => 'Result Analysis',
                        'description' => 'How do you rate success? We do it quite well!',
                        'icon' => 'icons/speed.svg',
                    ],
                    [
                        'title' => 'Extension Creation',
                        'description' => 'Extensions improve your ranking heavily! We do it!',
                        'icon' => 'icons/audit.svg',
                    ],
                    [
                        'title' => 'Broken Link Recovery',
                        'description' => 'The process is quite long and we do it effectively!',
                        'icon' => 'icons/megaphone.svg',
                    ],
                    [
                        'title' => 'Niche Edits',
                        'description' => 'Our niche edits make sure you get the best value!',
                        'icon' => 'icons/speed.svg',
                    ],
                    [
                        'title' => 'Digital Press Release',
                        'description' => 'Digital PRs require a lot of time and expertise!',
                        'icon' => 'icons/audit.svg',
                    ],
                    [
                        'title' => 'Guest Posting',
                        'description' => 'We offer the best guest posting services!',
                        'icon' => 'icons/speed.svg',
                    ],
                ],
            ],
            [
                'title' => 'Content Writing',
                'slug' => 'content-writing',
                'subtitle' => 'Topical authority that ranks and converts.',
                'description' => 'We don’t just write blogs; we build topical dominance. Every piece is engineered with TF-IDF optimization and search intent mapping.',
                'thumbnail' => 'images/seo-thumbnail.jpg',
                'status' => true,
                'is_campaign' => true,
                'has_faq' => false,
                'has_secondary_features' => false,
                'has_benifite' => true,
                'has_why_chose_us' => false,
                'faqs' => [],
                'section_one' => [
                    'title' => 'We Are Committed To Your Strategy',
                    'subtitle' => 'Strategic Alignment',
                    'description' => 'We Are Committed To Your Strategy And Intuitively Understand How To Deliver Value In The Digital Economy. Through The Most Effective Digital Marketing Options, Panelsbance Makes It Happen Seamlessly. Every Day, We Help Brands Think Big, Execute Smart And Deliver Growth. We Employ An Intelligent Digital Marketing Strategy To Consistently Unlock Value From Digital Investments In A Rapidly Advancing World, From Simple To The Infinitely Complex.',
                    'points' => [],
                    'button_text' => 'MORE ABOUT OUR COMPANY',
                    'image' => 'images/seo-benefits.jpg',
                ],
                'section_two' => null,
              
                'service_features' => [
                    [
                        'title' => 'Visibility',
                        'description' => 'Build topical authority, long-tail rank growth, and knowledge graph presence.',
                        'icon' => 'icons/speed.svg',
                    ],
                    [
                        'title' => 'Authority',
                        'description' => 'Publish well-researched pieces that establish industry expertise.',
                        'icon' => 'icons/audit.svg',
                    ],
                    [
                        'title' => 'Conversion',
                        'description' => 'Drive readers to take key actions and purchase product offerings.',
                        'icon' => 'icons/megaphone.svg',
                    ],
                ],
                'expect_results' => [
                    [
                        'value' => '3%',
                        'subtitle' => 'CTR Growth',
                        'title' => 'CTR Growth',
                        'icon' => 'ArrowUp',
                    ],
                    [
                        'value' => '100%',
                        'subtitle' => 'Organic Traffic',
                        'title' => 'Organic Traffic',
                        'icon' => 'ArrowUp',
                    ],
                    [
                        'value' => '15m',
                        'subtitle' => 'Impressions',
                        'title' => 'Impressions',
                        'icon' => 'ArrowUp',
                    ],
                    [
                        'value' => '282,000+',
                        'subtitle' => 'Leads generated so far...',
                        'title' => 'Total Leads',
                        'icon' => 'Contact us',
                    ],
                ],
                'has_brands' => true,
                'has_expect_result' => true,
                'brands' => [
                    ['name' => 'Notion', 'logo' => 'images/brands/notion.png'],
                    ['name' => 'Intercom', 'logo' => 'images/brands/intercom.png'],
                    ['name' => 'Figma', 'logo' => 'images/brands/figma.png'],
                    ['name' => 'Zapier', 'logo' => 'images/brands/zapier.png'],
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            Service::updateOrCreate(
                ['slug' => $serviceData['slug'] ?? Str::slug($serviceData['title'])],
                $serviceData
            );
        }
    }
}
