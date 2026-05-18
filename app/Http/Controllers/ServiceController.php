<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function show($slug)
    {
        $service = Service::where('slug', $slug)
            ->with(['pricingPlans', 'campaigns.tiers.features'])
            ->firstOrFail();
            
        return Inertia::render('ServiceDetails', [
            'service' => $service
        ]);
    }
}
