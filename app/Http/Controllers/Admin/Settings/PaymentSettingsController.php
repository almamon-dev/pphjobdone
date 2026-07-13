<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Artisan;

class PaymentSettingsController extends Controller
{
    public function edit()
    {
        return Inertia::render('Admin/Settings/Payment', [
            'settings' => [
                'stripe_key' => env('STRIPE_KEY', ''),
                'stripe_secret' => env('STRIPE_SECRET', ''),
                'stripe_webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
            ]
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'stripe_key' => 'nullable|string',
            'stripe_secret' => 'nullable|string',
            'stripe_webhook_secret' => 'nullable|string',
        ]);

        $envData = [
            'STRIPE_KEY' => $validated['stripe_key'] ?? '',
            'STRIPE_SECRET' => $validated['stripe_secret'] ?? '',
            'STRIPE_WEBHOOK_SECRET' => $validated['stripe_webhook_secret'] ?? '',
        ];

        $this->updateEnv($envData);

        // Clear config cache to apply changes
        Artisan::call('config:clear');

        return back()->with('success', 'Payment settings updated successfully.');
    }

    protected function updateEnv(array $data)
    {
        $path = base_path('.env');

        if (!file_exists($path)) {
            return;
        }

        $content = file_get_contents($path);

        foreach ($data as $key => $value) {
            // Escape special characters in value for regex
            $escapedValue = str_replace('$', '\$', $value);
            
            // If value contains spaces or special chars, wrap in quotes
            if (preg_match('/\s/', $value) || preg_match('/[#&]/', $value)) {
                $value = '"' . $value . '"';
            }

            if (preg_match("/^{$key}=/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }

        file_put_contents($path, $content);
    }
}
