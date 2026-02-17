<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'description',
        'icon',
        'thumbnail',
        'features',
        'pricing_plans',
        'faqs',
        'status',
    ];

    protected $casts = [
        'features' => 'array',
        'pricing_plans' => 'array',
        'faqs' => 'array',
        'status' => 'boolean',
    ];
}
