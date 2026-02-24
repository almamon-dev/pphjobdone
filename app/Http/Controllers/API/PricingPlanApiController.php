<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use App\Traits\ApiResponse;

class PricingPlanApiController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = PricingPlan::where('status', true)->get();

        return $this->sendResponse($plans, 'Plans retrieved successfully');
    }
}
