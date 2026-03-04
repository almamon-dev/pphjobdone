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
        'status',
    ];

    protected $casts = [
        'features' => 'array',
        'is_popular' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * The services that belong to the pricing plan.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'pricing_plan_service');
    }
}
