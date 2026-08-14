<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    protected $fillable = [
        'category',
        'title',
        'stats',
        'image',
        'video_bg_image',
        'client_type',
        'location',
        'service_provided',
        'duration',
        'challenge_description',
        'challenge_items',
        'approach_description',
        'approach_items',
        'results_description',
        'results_items',
        'is_active',
    ];

    protected $casts = [
        'challenge_items' => 'array',
        'approach_items' => 'array',
        'results_items' => 'array',
        'is_active' => 'boolean',
    ];
}
