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

    /**
     * Generate AI Proposal
     */
    public function generateAiProposal(\Illuminate\Http\Request $request, \App\Services\OpenAiService $openAiService)
    {
        $request->validate([
            'service_slug' => 'required|exists:services,slug',
            'lead_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'website_url' => 'nullable|url',
            'goals' => 'required|string',
            'budget' => 'nullable|string'
        ]);

        $service = Service::where('slug', $request->service_slug)->first();

        $leadData = $request->only(['lead_name', 'company_name', 'website_url', 'goals', 'budget']);
        
        $serviceData = [
            'title' => $service->title,
            'description' => $service->short_description,
            'pricing_plans' => $service->pricingPlans->map(fn($p) => $p->plan_name . ' - $' . $p->price_monthly)->toArray()
        ];

        $proposal = $openAiService->generateProposal($leadData, $serviceData);

        if (isset($proposal['error'])) {
            return $this->sendError($proposal['error'], [], 400);
        }

        // Return the proposal data. In a real app, this might generate a PDF using DomPDF.
        return $this->sendResponse($proposal, 'Personalized AI proposal generated successfully');
    }
}
