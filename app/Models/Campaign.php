<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'service_id',
        'title',
        'subtitle',
        'status',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function tiers()
    {
        return $this->hasMany(CampaignTier::class)->orderBy('price', 'asc');
    }
}
