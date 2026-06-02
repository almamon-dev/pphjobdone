<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query();

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%")
                ->orWhere('subtitle', 'like', "%{$request->search}%");
        }

        // Query removed

        if ($request->min_price || $request->max_price) {
            $query->whereHas('pricingPlans', function ($q) use ($request) {
                if ($request->min_price) {
                    $q->where('price', '>=', $request->min_price);
                }
                if ($request->max_price) {
                    $q->where('price', '<=', $request->max_price);
                }
            });
        }

        return Inertia::render('Admin/Services/Index', [
            'services' => $query->latest()->paginate($request->per_page ?? 15)->withQueryString(),
            'filters' => $request->only(['search', 'per_page', 'min_price', 'max_price']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Services/Create', [
            'pricing_plans' => \App\Models\PricingPlan::select('id', 'name', 'price')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'video_url' => 'nullable|string|max:500',
            'video_file' => 'nullable|mimes:mp4,mov,ogg,qt|max:20000',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'faqs' => 'nullable|array',
            'process_steps' => 'nullable|array',
            'section_one' => 'nullable|array',
            'section_two' => 'nullable|array',
            'service_features' => 'nullable|array',
            'timeline' => 'nullable|array',
            'expect_results' => 'nullable|array',
            'status' => 'required|boolean',
            'is_campaign' => 'required|boolean',
            'has_faq' => 'required|boolean',
            'has_secondary_features' => 'required|boolean',
            'has_benifite' => 'required|boolean',
            'has_why_chose_us' => 'required|boolean',
            'has_brands' => 'required|boolean',
            'has_expect_result' => 'required|boolean',
            'secondary_features' => 'nullable|array',
            'brands' => 'nullable|array',
            'pricing_plan_ids' => 'nullable|array',
            'pricing_plan_ids.*' => 'exists:pricing_plans,id',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('video_file')) {
            $validated['video_url'] = Helper::uploadFile('services/videos', $request->file('video_file'));
        }
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = Helper::uploadFile('services/thumbnails', $request->file('thumbnail'));
        }

        if ($request->hasFile('section_one_image')) {
            $sectionOne = $validated['section_one'] ?? [];
            $sectionOne['image'] = Helper::uploadFile('services/sections', $request->file('section_one_image'));
            $validated['section_one'] = $sectionOne;
        }

        if ($request->hasFile('section_two_image')) {
            $sectionTwo = $validated['section_two'] ?? [];
            $sectionTwo['image'] = Helper::uploadFile('services/sections', $request->file('section_two_image'));
            $validated['section_two'] = $sectionTwo;
        }

        if ($request->has('process_steps')) {
            $steps = $validated['process_steps'];
            foreach ($steps as $index => $step) {
                if ($request->hasFile("process_steps.$index.icon")) {
                    $steps[$index]['icon'] = Helper::uploadFile('services/process', $request->file("process_steps.$index.icon"));
                }
            }
            $validated['process_steps'] = $steps;
        }

        if ($request->has('service_features')) {
            $benefits = $validated['service_features'];
            foreach ($benefits as $index => $benefit) {
                if ($request->hasFile("service_features.$index.icon")) {
                    $benefits[$index]['icon'] = Helper::uploadFile('services/benefits', $request->file("service_features.$index.icon"));
                }
            }
            $validated['service_features'] = $benefits;
        }

        if ($request->has('secondary_features')) {
            $secFeatures = $validated['secondary_features'];
            foreach ($secFeatures as $index => $feature) {
                if ($request->hasFile("secondary_features.$index.icon")) {
                    $secFeatures[$index]['icon'] = Helper::uploadFile('services/secondary_features', $request->file("secondary_features.$index.icon"));
                }
            }
            $validated['secondary_features'] = $secFeatures;
        }

        if ($request->has('brands')) {
            $brands = $validated['brands'];
            foreach ($brands as $index => $brand) {
                if ($request->hasFile("brands.$index.logo")) {
                    $brands[$index]['logo'] = Helper::uploadFile('services/brands', $request->file("brands.$index.logo"));
                }
            }
            $validated['brands'] = $brands;
        }

        $service = Service::create(collect($validated)->except('pricing_plan_ids')->toArray());
        $service->pricingPlans()->sync($request->input('pricing_plan_ids', []));

        // range features removed

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        return Inertia::render('Admin/Services/Edit', [
            'service' => $service->load('pricingPlans'),
            'pricing_plans' => \App\Models\PricingPlan::select('id', 'name', 'price')->get(),
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'video_url' => 'nullable|string|max:500',
            'video_file' => 'nullable|mimes:mp4,mov,ogg,qt|max:20000',
            'faqs' => 'nullable|array',
            'process_steps' => 'nullable|array',
            'section_one' => 'nullable|array',
            'section_two' => 'nullable|array',
            'service_features' => 'nullable|array',
            'timeline' => 'nullable|array',
            'expect_results' => 'nullable|array',
            'status' => 'required|boolean',
            'is_campaign' => 'required|boolean',
            'has_faq' => 'required|boolean',
            'has_secondary_features' => 'required|boolean',
            'has_benifite' => 'required|boolean',
            'has_why_chose_us' => 'required|boolean',
            'has_brands' => 'required|boolean',
            'has_expect_result' => 'required|boolean',
            'secondary_features' => 'nullable|array',
            'brands' => 'nullable|array',
            'pricing_plan_ids' => 'nullable|array',
            'pricing_plan_ids.*' => 'exists:pricing_plans,id',
        ]);
        if ($request->hasFile('video_file')) {
            // Delete old video if it was a local file
            if ($service->video_url && ! filter_var($service->video_url, FILTER_VALIDATE_URL)) {
                Helper::deleteFile($service->video_url);
            }
            $validated['video_url'] = Helper::uploadFile('services/videos', $request->file('video_file'));
        }

        if ($request->hasFile('thumbnail')) {
            $request->validate(['thumbnail' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
            Helper::deleteFile($service->thumbnail);
            $validated['thumbnail'] = Helper::uploadFile('services/thumbnails', $request->file('thumbnail'));
        }

        if ($request->hasFile('section_one_image')) {
            $request->validate(['section_one_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
            $sectionOne = $validated['section_one'] ?? [];
            if (isset($service->section_one['image'])) {
                Helper::deleteFile($service->section_one['image']);
            }
            $sectionOne['image'] = Helper::uploadFile('services/sections', $request->file('section_one_image'));
            $validated['section_one'] = $sectionOne;
        } else {
            // Keep existing image if not uploading new one
            $sectionOne = $validated['section_one'] ?? [];
            if (isset($service->section_one['image'])) {
                $sectionOne['image'] = $service->section_one['image'];
            }
            $validated['section_one'] = $sectionOne;
        }

        if ($request->hasFile('section_two_image')) {
            $request->validate(['section_two_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
            $sectionTwo = $validated['section_two'] ?? [];
            if (isset($service->section_two['image'])) {
                Helper::deleteFile($service->section_two['image']);
            }
            $sectionTwo['image'] = Helper::uploadFile('services/sections', $request->file('section_two_image'));
            $validated['section_two'] = $sectionTwo;
        } else {
            // Keep existing image if not uploading new one
            $sectionTwo = $validated['section_two'] ?? [];
            if (isset($service->section_two['image'])) {
                $sectionTwo['image'] = $service->section_two['image'];
            }
            $validated['section_two'] = $sectionTwo;
        }

        if ($request->has('process_steps')) {
            $steps = $validated['process_steps'];
            $oldSteps = $service->process_steps ?? [];
            foreach ($steps as $index => $step) {
                if ($request->hasFile("process_steps.$index.icon")) {
                    if (isset($oldSteps[$index]['icon'])) {
                        Helper::deleteFile($oldSteps[$index]['icon']);
                    }
                    $steps[$index]['icon'] = Helper::uploadFile('services/process', $request->file("process_steps.$index.icon"));
                } else {
                    if (isset($oldSteps[$index]['icon'])) {
                        $steps[$index]['icon'] = $oldSteps[$index]['icon'];
                    }
                }
            }
            $validated['process_steps'] = $steps;
        }

        if ($request->has('service_features')) {
            $benefits = $validated['service_features'];
            $oldBenefits = $service->service_features ?? [];
            foreach ($benefits as $index => $benefit) {
                if ($request->hasFile("service_features.$index.icon")) {
                    if (isset($oldBenefits[$index]['icon'])) {
                        Helper::deleteFile($oldBenefits[$index]['icon']);
                    }
                    $benefits[$index]['icon'] = Helper::uploadFile('services/benefits', $request->file("service_features.$index.icon"));
                } else {
                    if (isset($oldBenefits[$index]['icon'])) {
                        $benefits[$index]['icon'] = $oldBenefits[$index]['icon'];
                    }
                }
            }
            $validated['service_features'] = $benefits;
        }

        if ($request->has('secondary_features')) {
            $secFeatures = $validated['secondary_features'];
            $oldSecFeatures = $service->secondary_features ?? [];
            foreach ($secFeatures as $index => $feature) {
                if ($request->hasFile("secondary_features.$index.icon")) {
                    if (isset($oldSecFeatures[$index]['icon'])) {
                        Helper::deleteFile($oldSecFeatures[$index]['icon']);
                    }
                    $secFeatures[$index]['icon'] = Helper::uploadFile('services/secondary_features', $request->file("secondary_features.$index.icon"));
                } else {
                    if (isset($oldSecFeatures[$index]['icon'])) {
                        $secFeatures[$index]['icon'] = $oldSecFeatures[$index]['icon'];
                    }
                }
            }
            $validated['secondary_features'] = $secFeatures;
        }

        if ($request->has('brands')) {
            $brands = $validated['brands'];
            $oldBrands = $service->brands ?? [];
            foreach ($brands as $index => $brand) {
                if ($request->hasFile("brands.$index.logo")) {
                    if (isset($oldBrands[$index]['logo'])) {
                        Helper::deleteFile($oldBrands[$index]['logo']);
                    }
                    $brands[$index]['logo'] = Helper::uploadFile('services/brands', $request->file("brands.$index.logo"));
                } else {
                    if (isset($oldBrands[$index]['logo'])) {
                        $brands[$index]['logo'] = $oldBrands[$index]['logo'];
                    }
                }
            }
            $validated['brands'] = $brands;
        }

        $service->update(collect($validated)->except('pricing_plan_ids')->toArray());
        $service->pricingPlans()->sync($request->input('pricing_plan_ids', []));

        // range features removed

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {

        if ($service->thumbnail) {
            Helper::deleteFile($service->thumbnail);
        }

        if ($service->video_url && ! filter_var($service->video_url, FILTER_VAR_VALIDATE_URL)) {
            Helper::deleteFile($service->video_url);
        }

        if ($service->process_steps) {
            foreach ($service->process_steps as $step) {
                if (isset($step['icon'])) {
                    Helper::deleteFile($step['icon']);
                }
            }
        }

        if (isset($service->section_one['image'])) {
            Helper::deleteFile($service->section_one['image']);
        }

        if (isset($service->section_two['image'])) {
            Helper::deleteFile($service->section_two['image']);
        }

        if ($service->service_features) {
            foreach ($service->service_features as $benefit) {
                if (isset($benefit['icon'])) {
                    Helper::deleteFile($benefit['icon']);
                }
            }
        }

        if ($service->brands) {
            foreach ($service->brands as $brand) {
                if (isset($brand['logo'])) {
                    Helper::deleteFile($brand['logo']);
                }
            }
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }
}
