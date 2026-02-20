<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'subtitle',
        'is_popular',
        'features',
        'button_text',
        'button_url',
        'status',
    ];

    protected $casts = [
        'features' => 'array',
        'is_popular' => 'boolean',
        'status' => 'boolean',
    ];
}
