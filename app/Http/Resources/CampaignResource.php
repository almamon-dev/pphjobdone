<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'status' => (bool) $this->status,
            'tiers' => $this->tiers->map(function ($tier) {
                return [
                    'id' => $tier->id,
                    'price' => (float) $tier->price,
                    'features' => $tier->features->map(function ($feature) {
                        return [
                            'id' => $feature->id,
                            'text' => $feature->feature_text,
                            'sub_items' => $feature->sub_items ?? [],
                        ];
                    }),
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
