<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'pricing_plan_id',
        'campaign_tier_id',
        'plan_name',
        'price',
        'status',
        'payment_status',
        'is_campaign',
        'campaign_details',
        'website_url',
        'target_keywords',
        'stripe_subscription_id',
        'stripe_customer_id',
        'stripe_status',
        'current_period_end',
        'cancel_at_period_end',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'campaign_details' => 'array',
        'is_campaign' => 'boolean',
        'cancel_at_period_end' => 'boolean',
        'current_period_end' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function pricingPlan()
    {
        return $this->belongsTo(PricingPlan::class);
    }

    public function campaignTier()
    {
        return $this->belongsTo(CampaignTier::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
