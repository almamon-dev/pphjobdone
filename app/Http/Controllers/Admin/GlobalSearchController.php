<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use App\Models\PricingPlan;
use App\Models\Campaign;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (empty($q) || strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // Search Services
        $services = Service::where('title', 'like', "%{$q}%")
            ->orWhere('subtitle', 'like', "%{$q}%")
            ->take(5)
            ->get();

        foreach ($services as $service) {
            $results[] = [
                'type' => 'Service',
                'title' => $service->title,
                'subtitle' => $service->subtitle ?? 'Service Item',
                'url' => route('admin.services.edit', $service->id),
                'icon' => 'Briefcase',
            ];
        }

        // Search Pricing Plans
        $plans = PricingPlan::where('name', 'like', "%{$q}%")
            ->take(5)
            ->get();

        foreach ($plans as $plan) {
            $results[] = [
                'type' => 'Pricing Plan',
                'title' => $plan->name,
                'subtitle' => 'Plan ($' . number_format($plan->price, 2) . ')',
                'url' => route('admin.pricing-plans.edit', $plan->id),
                'icon' => 'CircleDollarSign',
            ];
        }

        // Search Campaigns
        $campaigns = Campaign::where('title', 'like', "%{$q}%")
            ->take(5)
            ->get();

        foreach ($campaigns as $campaign) {
            $results[] = [
                'type' => 'Campaign',
                'title' => $campaign->title,
                'subtitle' => 'Campaign Group: ' . ($campaign->group_name ?? 'Default'),
                'url' => route('admin.campaigns.edit', $campaign->id),
                'icon' => 'Zap',
            ];
        }

        // Search Users
        $users = User::where('name', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->take(5)
            ->get();

        foreach ($users as $user) {
            $results[] = [
                'type' => 'User',
                'title' => $user->name,
                'subtitle' => $user->email,
                'url' => route('admin.users.index', ['search' => $user->email]),
                'icon' => 'Users',
            ];
        }

        // Search Contacts / Messages
        $contacts = ContactMessage::where('first_name', 'like', "%{$q}%")
            ->orWhere('last_name', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->take(5)
            ->get();

        foreach ($contacts as $contact) {
            $results[] = [
                'type' => 'Contact',
                'title' => trim(($contact->first_name ?? '') . ' ' . ($contact->last_name ?? '')),
                'subtitle' => $contact->email,
                'url' => route('admin.contacts.index', ['search' => $contact->email]),
                'icon' => 'Mail',
            ];
        }

        return response()->json(['results' => $results]);
    }
}
