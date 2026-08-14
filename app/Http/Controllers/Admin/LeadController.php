<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::query()->latest();

        if ($request->has('qualification') && in_array($request->qualification, ['Hot', 'Warm', 'Cold'])) {
            $query->where('qualification_status', $request->qualification);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('service_interest', 'like', "%{$search}%");
            });
        }

        $leads = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Leads/Index', [
            'leads' => $leads,
            'filters' => $request->only(['search', 'qualification']),
            'stats' => [
                'total' => Lead::count(),
                'hot' => Lead::where('qualification_status', 'Hot')->count(),
                'warm' => Lead::where('qualification_status', 'Warm')->count(),
                'cold' => Lead::where('qualification_status', 'Cold')->count(),
            ]
        ]);
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,converted,closed',
        ]);

        $lead->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Lead status updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return back()->with('success', 'Lead deleted successfully.');
    }
}
