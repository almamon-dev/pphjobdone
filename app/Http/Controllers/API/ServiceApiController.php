<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Traits\ApiResponse;

class ServiceApiController extends Controller
{
    use ApiResponse;

    /**
     * Get all services list
     */
    public function index()
    {
        $query = Service::where('status', true);

        // Query removed

        $services = $query->latest()->get();

        return $this->sendResponse(ServiceResource::collection($services), 'Services fetched successfully');
    }

    /**
     * Get single service details by slug
     */
    public function show($slug)
    {
        $service = Service::where('slug', $slug)
            ->where('status', true)
            ->first();

        if (! $service) {
            return $this->sendError('Service not found');
        }

        return $this->sendResponse(new \App\Http\Resources\ServiceDetailResource($service), 'Service fetched successfully');
    }

    /**
     * Get single service proposal by slug
     */
    public function proposal($slug)
    {
        $service = Service::where('slug', $slug)
            ->where('status', true)
            ->first();

        if (! $service) {
            return $this->sendError('Service not found');
        }

        return $this->sendResponse(new \App\Http\Resources\ServiceProposalResource($service), 'Service proposal fetched successfully');
    }
}
