<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'icon',
        'description',
        'video_url',
        'video_file',
        'thumbnail',
        'features',
        'faqs',
        'process_steps',
        'section_one',
        'section_two',
        'service_features',
        'secondary_features',
        'timeline',
        'expect_results',
        'brands',
        'status',
        'is_campaign',
        'has_faq',
        'has_secondary_features',
        'has_benifite',
        'has_why_chose_us',
        'has_brands',
        'has_expect_result',
    ];

    protected $casts = [
        'features' => 'array',
        'faqs' => 'array',
        'process_steps' => 'array',
        'section_one' => 'array',
        'section_two' => 'array',
        'service_features' => 'array',
        'secondary_features' => 'array',
        'timeline' => 'array',
        'expect_results' => 'array',
        'brands' => 'array',
        'status' => 'boolean',
        'is_campaign' => 'boolean',
        'has_faq' => 'boolean',
        'has_secondary_features' => 'boolean',
        'has_benifite' => 'boolean',
        'has_why_chose_us' => 'boolean',
        'has_brands' => 'boolean',
        'has_expect_result' => 'boolean',
    ];


    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function pricingPlans()
    {
        return $this->belongsToMany(PricingPlan::class, 'pricing_plan_service');
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}
