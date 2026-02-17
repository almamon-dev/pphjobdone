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

        return Inertia::render('Admin/Services/Index', [
            'services' => $query->latest()->paginate($request->per_page ?? 15)->withQueryString(),
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Services/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'features' => 'nullable|array',
            'pricing_plans' => 'nullable|array',
            'faqs' => 'nullable|array',
            'status' => 'required|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('icon')) {
            $validated['icon'] = Helper::uploadFile('services/icons', $request->file('icon'));
        }

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = Helper::uploadFile('services/thumbnails', $request->file('thumbnail'));
        }

        Service::create($validated);

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        return Inertia::render('Admin/Services/Edit', [
            'service' => $service,
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'pricing_plans' => 'nullable|array',
            'faqs' => 'nullable|array',
            'status' => 'required|boolean',
        ]);

        if ($request->hasFile('icon')) {
            $request->validate(['icon' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
            Helper::deleteFile($service->icon);
            $validated['icon'] = Helper::uploadFile('services/icons', $request->file('icon'));
        }

        if ($request->hasFile('thumbnail')) {
            $request->validate(['thumbnail' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
            Helper::deleteFile($service->thumbnail);
            $validated['thumbnail'] = Helper::uploadFile('services/thumbnails', $request->file('thumbnail'));
        }

        $validated['slug'] = Str::slug($validated['title']);

        $service->update($validated);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        if ($service->icon) {
            Helper::deleteFile($service->icon);
        }
        if ($service->thumbnail) {
            Helper::deleteFile($service->thumbnail);
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }
}
