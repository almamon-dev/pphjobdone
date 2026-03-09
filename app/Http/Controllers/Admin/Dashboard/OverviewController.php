<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class OverviewController extends Controller
{
    public function index()
    {
        // Counts
        $usersCount = User::count();
        $servicesCount = \App\Models\Service::count();

        if (auth()->user()->is_admin) {
            return Inertia::render('Admin/Dashboard', [
                'stats' => [
                    'users' => $usersCount,
                    'services' => $servicesCount,
                    'bookings' => \App\Models\Booking::count(),
                ]
            ]);
        }

        return Inertia::render('Dashboard');
    }
}
