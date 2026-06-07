<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/services/{slug}', [\App\Http\Controllers\ServiceController::class, 'show'])->name('services.show');

Route::get('/test-payment', function () {
    $token = auth()->user()->createToken('test-payment')->plainTextToken;
    return Inertia::render('TestPayment', [
        'token' => $token
    ]);
})->middleware(['auth']);

use App\Http\Controllers\Admin\Dashboard\OverviewController;

Route::get('/dashboard', [OverviewController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

use App\Http\Controllers\Admin\Settings\SystemSettingsController;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Settings
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/settings/system', [SystemSettingsController::class, 'edit'])->name('settings.system');
        Route::post('/settings/system', [SystemSettingsController::class, 'update'])->name('settings.system.update');
        // Users
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->only(['index', 'destroy']);
        // Services
        Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class);
        // Pricing Plans
        Route::resource('pricing-plans', \App\Http\Controllers\Admin\PricingPlanController::class);
        // Campaigns
        Route::post('campaigns/{campaign}/duplicate', [\App\Http\Controllers\Admin\CampaignController::class, 'duplicate'])->name('campaigns.duplicate');
        Route::resource('campaigns', \App\Http\Controllers\Admin\CampaignController::class);
        // Contacts
        Route::resource('contacts', \App\Http\Controllers\Admin\ContactController::class)->only(['index', 'destroy']);
    });
});

require __DIR__.'/auth.php';
