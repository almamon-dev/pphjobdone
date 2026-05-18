<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceDetailResource extends JsonResource
{
    /**
     * Format asset paths safely to prevent double host duplication.
     */
    private function formatUrl($path)
    {
        if (!$path) {
            return null;
        }
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        return asset($path);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Safe null guards for JSON section columns — prevents crash when null
        $sectionOne = is_array($this->section_one) ? $this->section_one : [];
        $sectionTwo = is_array($this->section_two) ? $this->section_two : [];

        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'slug'        => $this->slug,
            'subtitle'    => $this->subtitle,
            'thumbnail'   => $this->formatUrl($this->thumbnail),
            'is_campaign' => $this->campaigns()->where('status', true)->exists(),

            // Video
            'video' => [
                'file' => ($this->video_url && !filter_var($this->video_url, FILTER_VALIDATE_URL))
                    ? $this->formatUrl($this->video_url)
                    : null,
                'url'  => ($this->video_url && filter_var($this->video_url, FILTER_VALIDATE_URL))
                    ? $this->video_url
                    : null,
            ],

            // Pricing Plans — scoped to THIS service only
            'pricing' => \App\Http\Resources\PricingPlanResource::collection(
                $this->pricingPlans()->where('status', true)->get()
            ),

            // Campaigns
            'campaigns' => \App\Http\Resources\CampaignResource::collection(
                $this->campaigns()->where('status', true)->get()
            ),

            // What's Included (from service_features column)
            'what_include' => collect($this->service_features ?? [])->map(function ($item) {
                return [
                    'title'       => $item['title']       ?? null,
                    'description' => $item['description'] ?? null,
                    'icon'        => $this->formatUrl($item['icon'] ?? null),
                ];
            })->values(),



            // Benefits Section (from section_one column)
            'banifite' => $this->when((bool) $this->has_benifite, [
                'badge'       => $sectionOne['subtitle']    ?? null,
                'title'       => $sectionOne['title']       ?? null,
                'description' => $sectionOne['description'] ?? null,
                'points'      => $sectionOne['points']      ?? [],
                'image'       => $this->formatUrl($sectionOne['image'] ?? null),
                'button_text' => $sectionOne['button_text'] ?? null,
                'button_url'  => $sectionOne['button_url']  ?? null,
            ]),

            // Secondary Features (from secondary_features column)
            'secondary_features' => $this->when((bool) $this->has_secondary_features, collect($this->secondary_features ?? [])->map(function ($feature) {
                return [
                    'title'       => $feature['title']       ?? null,
                    'description' => $feature['description'] ?? null,
                    'icon'        => $this->formatUrl($feature['icon'] ?? null),
                ];
            })->values()),

            // Why Choose Us Section (from section_two column)
            'why_chose_us' => $this->when((bool) $this->has_why_chose_us, [
                'badge'       => $sectionTwo['subtitle']    ?? null,
                'title'       => $sectionTwo['title']       ?? null,
                'description' => $sectionTwo['description'] ?? null,
                'points'      => $sectionTwo['points']      ?? [],
                'image'       => $this->formatUrl($sectionTwo['image'] ?? null),
                'button_text' => $sectionTwo['button_text'] ?? null,
                'button_url'  => $sectionTwo['button_url']  ?? null,
            ]),

            // FAQs (from faqs column)
            'faq' => $this->when((bool) $this->has_faq, collect($this->faqs ?? [])->map(function ($faq) {
                return [
                    'question' => $faq['question'] ?? null,
                    'answer'   => $faq['answer']   ?? null,
                ];
            })->values()),

            // Proposed Timeline (from timeline column)
            'timeline' => $this->when(!empty($this->timeline), collect($this->timeline ?? [])->map(function ($phase, $index) {
                return [
                    'phase_number' => str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                    'title'        => $phase['title']    ?? null,
                    'duration'     => $phase['duration'] ?? null,
                    'price'        => $phase['price']    ?? null,
                    'items'        => $phase['items']    ?? [],
                ];
            })->values()),

            // Expected Results (from expect_results column)
            'expect_results' => $this->when((bool) $this->has_expect_result, collect($this->expect_results ?? [])->map(function ($result) {
                return [
                    'title'    => $result['title']    ?? null,
                    'value'    => $result['value']    ?? null,
                    'subtitle' => $result['subtitle'] ?? null,
                    'icon'     => $result['icon']     ?? 'ArrowUpRight',
                ];
            })->values()),

            // Brand Logos (from brands column)
            'brands' => $this->when((bool) $this->has_brands, collect($this->brands ?? [])->map(function ($brand) {
                return [
                    'name' => $brand['name'] ?? null,
                    'logo' => $this->formatUrl($brand['logo'] ?? null),
                ];
            })->values()),

            'status'     => (bool) $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
