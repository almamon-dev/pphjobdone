<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use App\Models\Service;
use App\Services\StripePlanService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PricingPlanController extends Controller
{
    protected StripePlanService $stripePlanService;

    public function __construct(StripePlanService $stripePlanService)
    {
        $this->stripePlanService = $stripePlanService;
    }

    public function index(Request $request)
    {
        $query = PricingPlan::query()->with('services');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('subtitle', 'like', "%{$request->search}%");
        }

        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        return Inertia::render('Admin/PricingPlans/Index', [
            'pricing_plans' => $query->latest()->paginate($request->per_page ?? 10)->withQueryString(),
            'filters' => $request->only(['search', 'per_page', 'min_price', 'max_price']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/PricingPlans/Create', [
            'services' => Service::select('id', 'title')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'name' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'billing_interval' => 'required|string|in:month,year',
            'subtitle' => 'nullable|string|max:255',
            'is_popular' => 'boolean',
            'features' => 'nullable|array',
            'button_text' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $plan = PricingPlan::create($validated);
        $plan->services()->sync($request->service_ids);

        // Auto Sync with Stripe Product & Price
        $synced = $this->stripePlanService->syncPlanToStripe($plan);

        $message = $synced
            ? 'Pricing plan created and synced with Stripe successfully.'
            : 'Pricing plan created locally. (Stripe sync skipped or pending credentials)';

        return redirect()->route('admin.pricing-plans.index')->with('success', $message);
    }

    public function edit(PricingPlan $pricingPlan)
    {
        return Inertia::render('Admin/PricingPlans/Edit', [
            'pricing_plan' => $pricingPlan->load('services'),
            'services' => Service::select('id', 'title')->get()
        ]);
    }

    public function update(Request $request, PricingPlan $pricingPlan)
    {
        $validated = $request->validate([
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'name' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'billing_interval' => 'required|string|in:month,year',
            'subtitle' => 'nullable|string|max:255',
            'is_popular' => 'boolean',
            'features' => 'nullable|array',
            'button_text' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $pricingPlan->update($validated);
        $pricingPlan->services()->sync($request->service_ids);

        // Auto Sync with Stripe Product & Price
        $synced = $this->stripePlanService->syncPlanToStripe($pricingPlan);

        $message = $synced
            ? 'Pricing plan updated and synced with Stripe successfully.'
            : 'Pricing plan updated locally. (Stripe sync skipped or pending credentials)';

        return redirect()->route('admin.pricing-plans.index')->with('success', $message);
    }

    public function destroy(PricingPlan $pricingPlan)
    {
        $this->stripePlanService->archiveStripePlan($pricingPlan);
        $pricingPlan->delete();

        return redirect()->route('admin.pricing-plans.index')->with('success', 'Pricing plan deleted and archived in Stripe successfully.');
    }

    public function resync(PricingPlan $pricingPlan)
    {
        $synced = $this->stripePlanService->syncPlanToStripe($pricingPlan);

        if ($synced) {
            return back()->with('success', 'Pricing plan synced with Stripe successfully.');
        }

        return back()->with('error', 'Failed to sync plan with Stripe. Please check your Stripe credentials.');
    }
}
