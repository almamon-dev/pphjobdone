<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignTier extends Model
{
    protected $fillable = [
        'campaign_id',
        'price',
        'status',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function features()
    {
        return $this->hasMany(CampaignFeature::class);
    }
}
