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

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users' => [
                    'count' => $usersCount,
                    'status' => '+MEMBERS',
                ],

                'services' => [
                    'count' => $servicesCount,
                    'status' => '+SERVICES',
                ],
            ],
            'portfolioHealth' => [
                'completion' => 100,
                'activeExperiences' => 'High',
                'verifiedSkills' => 'Active',
            ],
            'openai_api_key' => env('OPENAI_API_KEY'),
        ]);
    }
}
