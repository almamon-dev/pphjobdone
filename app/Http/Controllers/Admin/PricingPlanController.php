<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PricingPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = PricingPlan::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('subtitle', 'like', "%{$request->search}%");
        }

        return Inertia::render('Admin/PricingPlans/Index', [
            'pricing_plans' => $query->latest()->paginate($request->per_page ?? 15)->withQueryString(),
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/PricingPlans/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'is_popular' => 'boolean',
            'features' => 'nullable|array',
            'button_text' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        PricingPlan::create($validated);

        return redirect()->route('admin.pricing-plans.index')->with('success', 'Pricing plan created successfully.');
    }

    public function edit(PricingPlan $pricingPlan)
    {
        return Inertia::render('Admin/PricingPlans/Edit', [
            'pricing_plan' => $pricingPlan,
        ]);
    }

    public function update(Request $request, PricingPlan $pricingPlan)
    {
        $validated = $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'name' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'is_popular' => 'boolean',
            'features' => 'nullable|array',
            'button_text' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $pricingPlan->update($validated);

        return redirect()->route('admin.pricing-plans.index')->with('success', 'Pricing plan updated successfully.');
    }

    public function destroy(PricingPlan $pricingPlan)
    {
        $pricingPlan->delete();

        return redirect()->route('admin.pricing-plans.index')->with('success', 'Pricing plan deleted successfully.');
    }
}
