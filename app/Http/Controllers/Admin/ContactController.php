<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\ContactMessage::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        $perPage = $request->input('per_page', 10);
        $contacts = $query->latest()->paginate($perPage)->withQueryString();

        return \Inertia\Inertia::render('Admin/Contacts/Index', [
            'contacts' => $contacts,
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function destroy(\App\Models\ContactMessage $contact)
    {
        $contact->delete();
        return redirect()->back()->with('success', 'Contact message deleted successfully.');
    }
}
