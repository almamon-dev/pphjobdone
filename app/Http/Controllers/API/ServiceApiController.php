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

        $proposalContent = $openAiService->generateProposal($leadData, $serviceData);

        if (isset($proposalContent['error'])) {
            return $this->sendError($proposalContent['error'], [], 400);
        }

        $user = auth('sanctum')->user();

        // Create or find lead
        $lead = null;
        if (!empty($request->email) || $user?->email) {
            $email = $request->email ?? $user->email;
            $lead = \App\Models\Lead::updateOrCreate(
                ['email' => $email],
                [
                    'user_id' => $user?->id,
                    'name' => $request->lead_name,
                    'company_name' => $request->company_name,
                    'service_interest' => $service->title,
                    'budget' => $request->budget,
                    'status' => 'new',
                ]
            );
        }

        // Persist proposal in database
        $proposalRecord = \App\Models\Proposal::create([
            'user_id' => $user?->id,
            'lead_id' => $lead?->id,
            'service_id' => $service->id,
            'title' => $service->title . ' Proposal for ' . $request->company_name,
            'lead_name' => $request->lead_name,
            'company_name' => $request->company_name,
            'website_url' => $request->website_url,
            'goals' => $request->goals,
            'budget' => $request->budget,
            'proposal_data' => $proposalContent,
            'status' => 'sent',
            'expires_at' => now()->addDays(30),
        ]);

        $responseData = array_merge($proposalContent, [
            'proposal_id' => $proposalRecord->id,
            'download_pdf' => url('/api/services/proposal/' . $proposalRecord->id . '/download'),
        ]);

        return $this->sendResponse($responseData, 'Personalized AI proposal generated and saved successfully');
    }

    /**
     * Download Proposal PDF
     */
    public function downloadProposalPdf($id)
    {
        $proposal = \App\Models\Proposal::with('service')->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.proposal', ['proposal' => $proposal]);

        return $pdf->download('Proposal-' . str_replace(' ', '_', $proposal->company_name ?? 'Client') . '.pdf');
    }

    /**
     * Get user saved proposals
     */
    public function getUserProposals()
    {
        $user = auth()->user();

        $proposals = \App\Models\Proposal::with('service')
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'title' => $p->title,
                    'company_name' => $p->company_name,
                    'service_name' => $p->service?->title,
                    'status' => $p->status,
                    'created_at' => $p->created_at->format('Y-m-d'),
                    'download_url' => url('/api/services/proposal/' . $p->id . '/download'),
                    'data' => $p->proposal_data,
                ];
            });

        return $this->sendResponse($proposals, 'User proposals fetched successfully');
    }
}

