<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Service;
use App\Models\CampaignTier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::with(['service', 'tiers']);

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        return Inertia::render('Admin/Campaigns/Index', [
            'campaigns' => $query->latest()->paginate($request->per_page ?? 10)->withQueryString(),
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Campaigns/Create', [
            'services' => Service::where('status', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'tiers' => 'required|array|min:1',
            'tiers.*.price' => 'required|numeric|min:0',
            'tiers.*.features' => 'required|array|min:1',
            'tiers.*.features.*.feature_text' => 'required|string|max:255',
            'tiers.*.features.*.sub_items' => 'nullable|array',
        ]);

        $campaign = Campaign::create([
            'service_id' => $validated['service_id'],
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'status' => $validated['status'],
        ]);

        foreach ($request->tiers as $tierData) {
            $tier = $campaign->tiers()->create([
                'price' => $tierData['price'],
                'status' => true,
            ]);

            foreach ($tierData['features'] as $feature) {
                $tier->features()->create([
                    'feature_text' => $feature['feature_text'],
                    'sub_items' => $feature['sub_items'] ?? [],
                    'status' => true,
                ]);
            }
        }

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign created successfully.');
    }

    public function edit(Campaign $campaign)
    {
        return Inertia::render('Admin/Campaigns/Edit', [
            'campaign' => $campaign->load(['tiers.features']),
            'services' => Service::where('status', true)->get(),
        ]);
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'tiers' => 'required|array|min:1',
            'tiers.*.price' => 'required|numeric|min:0',
            'tiers.*.features' => 'required|array|min:1',
            'tiers.*.features.*.feature_text' => 'required|string|max:255',
            'tiers.*.features.*.sub_items' => 'nullable|array',
        ]);

        $campaign->update([
            'service_id' => $validated['service_id'],
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'status' => $validated['status'],
        ]);

        // Simple approach: Delete existing tiers and recreate
        // (In production, you'd want to sync by ID to preserve historical data)
        $campaign->tiers()->delete();

        foreach ($request->tiers as $tierData) {
            $tier = $campaign->tiers()->create([
                'price' => $tierData['price'],
                'status' => true,
            ]);

            foreach ($tierData['features'] as $feature) {
                $tier->features()->create([
                    'feature_text' => $feature['feature_text'],
                    'sub_items' => $feature['sub_items'] ?? [],
                    'status' => true,
                ]);
            }
        }

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign deleted successfully.');
    }

    public function duplicate(Campaign $campaign)
    {
        $newCampaign = $campaign->replicate();
        $newCampaign->title = $campaign->title . ' (Copy)';
        $newCampaign->save();

        foreach ($campaign->load('tiers.features')->tiers as $tier) {
            $newTier = $newCampaign->tiers()->create([
                'price' => $tier->price,
                'status' => $tier->status,
            ]);

            foreach ($tier->features as $feature) {
                $newTier->features()->create([
                    'feature_text' => $feature->feature_text,
                    'sub_items' => $feature->sub_items,
                    'status' => $feature->status,
                ]);
            }
        }

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign duplicated successfully.');
    }
}
