<?php

use Illuminate\Support\Facades\Route;

// Authentication
Route::prefix('auth')->middleware(['auth.rate.limit'])->group(function () {
    Route::post('login', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'loginApi']);
    Route::post('register', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'registerApi']);
    Route::post('verify-email', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'verifyEmailApi']);
    Route::post('forgot-password', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'forgotPasswordApi']);
    Route::post('reset-password', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'resetPasswordApi']);
    Route::post('resend-otp', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'resendOtpApi']);
    Route::post('verify-otp', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'verifyOtpApi']);
});

// AI SEO Audit
Route::post('seo-audit', [\App\Http\Controllers\API\SeoAuditController::class, 'audit']);
Route::post('seo-audit/download', [\App\Http\Controllers\API\SeoAuditController::class, 'downloadPdf']);

// Services
Route::get('services', [\App\Http\Controllers\API\ServiceApiController::class, 'index']);
Route::get('services/details/{slug}', [\App\Http\Controllers\API\ServiceApiController::class, 'show']);
Route::get('services/proposal/{slug}', [\App\Http\Controllers\API\ServiceApiController::class, 'proposal']);

// Bookings & Payments
Route::post('bookings/create', [\App\Http\Controllers\API\BookingApiController::class, 'store'])->middleware('auth:sanctum');
Route::any('payments/webhook', [\App\Http\Controllers\API\BookingApiController::class, 'webhook']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('auth/logout', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'logoutApi']);
    // Profile Actions
    Route::get('profile', [\App\Http\Controllers\API\ProfileApiController::class, 'show']);
    Route::post('profile/update', [\App\Http\Controllers\API\ProfileApiController::class, 'update']);
    Route::post('profile/setup', [\App\Http\Controllers\API\ProfileApiController::class, 'setupProfile']);
    Route::post('profile/update-password', [\App\Http\Controllers\API\ProfileApiController::class, 'updatePassword']);
    // User Dashboard
    Route::get('user/dashboard', [\App\Http\Controllers\API\UserDashboardApiController::class, 'index']);
    // User Services & Bookings
    Route::get('user/services', [\App\Http\Controllers\API\UserServicesApiController::class, 'index']);
    Route::get('user/reports', [\App\Http\Controllers\API\UserReportsApiController::class, 'index']);
    Route::get('user/tasks', [\App\Http\Controllers\API\UserTasksApiController::class, 'index']);
    Route::get('user/bookings', [\App\Http\Controllers\API\BookingApiController::class, 'index']);
    Route::get('user/bookings/{id}', [\App\Http\Controllers\API\BookingApiController::class, 'show']);
});
