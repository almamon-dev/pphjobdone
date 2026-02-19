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
        'pricing_plans',
        'faqs',
        'process_steps',
        'section_one',
        'section_two',
        'benefits',
        'timeline',
        'expect_results',
        'status',
    ];

    protected $casts = [
        'features' => 'array',
        'pricing_plans' => 'array',
        'faqs' => 'array',
        'process_steps' => 'array',
        'section_one' => 'array',
        'section_two' => 'array',
        'benefits' => 'array',
        'timeline' => 'array',
        'expect_results' => 'array',
        'status' => 'boolean',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
