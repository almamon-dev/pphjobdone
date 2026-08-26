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
        Route::get('/search', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'search'])->name('global-search');
        Route::get('/settings/system', [SystemSettingsController::class, 'edit'])->name('settings.system');
        Route::post('/settings/system', [SystemSettingsController::class, 'update'])->name('settings.system.update');
        Route::get('/settings/payment', [\App\Http\Controllers\Admin\Settings\PaymentSettingsController::class, 'edit'])->name('settings.payment');
        Route::post('/settings/payment', [\App\Http\Controllers\Admin\Settings\PaymentSettingsController::class, 'update'])->name('settings.payment.update');
        // Users
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->only(['index', 'destroy']);
        // Services
        Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class);
        // Pricing Plans
        Route::post('pricing-plans/{pricingPlan}/resync', [\App\Http\Controllers\Admin\PricingPlanController::class, 'resync'])->name('pricing-plans.resync');
        Route::resource('pricing-plans', \App\Http\Controllers\Admin\PricingPlanController::class);
        // Campaigns & Case Studies
        Route::post('campaigns/{campaign}/duplicate', [\App\Http\Controllers\Admin\CampaignController::class, 'duplicate'])->name('campaigns.duplicate');
        Route::resource('campaigns', \App\Http\Controllers\Admin\CampaignController::class);
        Route::resource('case-studies', \App\Http\Controllers\Admin\CaseStudyController::class);
        // Contacts, AI Leads & Live Messages
        Route::resource('contacts', \App\Http\Controllers\Admin\ContactController::class)->only(['index', 'destroy']);
        Route::get('leads', [\App\Http\Controllers\Admin\LeadController::class, 'index'])->name('leads.index');
        Route::patch('leads/{lead}/status', [\App\Http\Controllers\Admin\LeadController::class, 'updateStatus'])->name('leads.status');
        Route::delete('leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'destroy'])->name('leads.destroy');
        Route::get('messages', [\App\Http\Controllers\Admin\AdminMessageController::class, 'index'])->name('messages.index');
        Route::post('messages/send', [\App\Http\Controllers\Admin\AdminMessageController::class, 'store'])->name('messages.store');
        Route::put('messages/{id}', [\App\Http\Controllers\Admin\AdminMessageController::class, 'update'])->name('messages.update');
        Route::delete('messages/{id}', [\App\Http\Controllers\Admin\AdminMessageController::class, 'destroy'])->name('messages.destroy');

        // Bookings & Tasks
        Route::post('bookings/{booking}/toggle-auto-renew', [\App\Http\Controllers\Admin\BookingController::class, 'toggleAutoRenew'])->name('bookings.toggle-auto-renew');
        Route::resource('bookings', \App\Http\Controllers\Admin\BookingController::class)->only(['index', 'show']);
        Route::post('bookings/{booking}/tasks', [\App\Http\Controllers\Admin\BookingController::class, 'storeTask'])->name('bookings.tasks.store');
        Route::put('bookings/{booking}/tasks/{task}', [\App\Http\Controllers\Admin\BookingController::class, 'updateTask'])->name('bookings.tasks.update');
        Route::delete('bookings/{booking}/tasks/{task}', [\App\Http\Controllers\Admin\BookingController::class, 'destroyTask'])->name('bookings.tasks.destroy');

        // Notifications
        Route::post('notifications/{id}/read', function ($id) {
            $notification = auth()->user()->notifications()->findOrFail($id);
            $notification->markAsRead();
            return back();
        })->name('notifications.read');
    });
});

require __DIR__ . '/auth.php';
