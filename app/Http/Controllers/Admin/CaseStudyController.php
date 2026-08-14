<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CaseStudyController extends Controller
{
    public function index(Request $request)
    {
        $query = CaseStudy::query()->latest();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('stats', 'like', "%{$search}%")
                  ->orWhere('client_type', 'like', "%{$search}%");
            });
        }

        $caseStudies = $query->paginate(10)->withQueryString();

        return Inertia::render('Admin/CaseStudies/Index', [
            'caseStudies' => $caseStudies,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/CaseStudies/Form', [
            'caseStudy' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'stats' => 'required|string|max:255',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|max:4096',
            'client_type' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'service_provided' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'challenge_description' => 'nullable|string',
            'approach_description' => 'nullable|string',
            'results_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('case-studies', 'public');
            $validated['image'] = '/storage/' . $path;
        } elseif (empty($validated['image'])) {
            $validated['image'] = '/storage/case-studies/p1.webp';
        }

        CaseStudy::create($validated);

        return redirect()->route('admin.case-studies.index')->with('success', 'Case Study created successfully.');
    }

    public function edit(CaseStudy $caseStudy)
    {
        return Inertia::render('Admin/CaseStudies/Form', [
            'caseStudy' => $caseStudy,
        ]);
    }

    public function update(Request $request, CaseStudy $caseStudy)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'stats' => 'required|string|max:255',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|max:4096',
            'client_type' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'service_provided' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'challenge_description' => 'nullable|string',
            'approach_description' => 'nullable|string',
            'results_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('case-studies', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        $caseStudy->update($validated);

        return redirect()->route('admin.case-studies.index')->with('success', 'Case Study updated successfully.');
    }

    public function destroy(CaseStudy $caseStudy)
    {
        $caseStudy->delete();

        return back()->with('success', 'Case Study deleted successfully.');
    }
}
