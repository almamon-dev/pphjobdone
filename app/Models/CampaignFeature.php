<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignFeature extends Model
{
    protected $fillable = [
        'campaign_tier_id',
        'feature_text',
        'sub_items',
        'status',
    ];

    protected $casts = [
        'sub_items' => 'array',
        'status' => 'boolean',
    ];

    public function tier()
    {
        return $this->belongsTo(CampaignTier::class, 'campaign_tier_id');
    }
}
