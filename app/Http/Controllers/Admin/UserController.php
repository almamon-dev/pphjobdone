<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->input('per_page', 10);

        $users = $query->withCount('bookings')
            ->withSum('bookings as total_spent', 'price')
            ->latest()
            ->paginate($perPage)
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? 'N/A',
                    'is_admin' => (bool) $user->is_admin,
                    'is_verified' => (bool) ($user->is_verified || $user->email_verified_at),
                    'is_subscribed' => (bool) $user->is_subscribed,
                    'bookings_count' => (int) ($user->bookings_count ?? 0),
                    'total_spent' => (float) ($user->total_spent ?? 0),
                    'created_at' => $user->created_at,
                    'profile_photo_url' => \App\Helpers\Helper::generateURL($user->avatar),
                ];
            })
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }
}
