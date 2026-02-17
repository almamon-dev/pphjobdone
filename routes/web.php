<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

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
    });
});

require __DIR__.'/auth.php';
