<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $notifications = [];
        $unreadCount = 0;

        if ($request->user() && $request->user()->role === 'admin') {
            $notifications = $request->user()->unreadNotifications()->take(5)->get();
            $unreadCount = $request->user()->unreadNotifications()->count();
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'adminNotifications' => [
                'list' => $notifications,
                'unreadCount' => $unreadCount,
            ],
        ];
    }
}
