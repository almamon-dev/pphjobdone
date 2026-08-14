<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

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
Route::get('seo-audit/download', [\App\Http\Controllers\API\SeoAuditController::class, 'downloadPdf']);
Route::get('campaign-report/download', [\App\Http\Controllers\API\UserReportsApiController::class, 'downloadCampaignReport']);

// Services
Route::get('services', [\App\Http\Controllers\API\ServiceApiController::class, 'index']);
Route::get('services/{slug}', [\App\Http\Controllers\API\ServiceApiController::class, 'show']);
Route::get('services/details/{slug}', [\App\Http\Controllers\API\ServiceApiController::class, 'show']);
Route::get('services/proposal/{slug}', [\App\Http\Controllers\API\ServiceApiController::class, 'proposal']);
Route::post('services/proposal/generate', [\App\Http\Controllers\API\ServiceApiController::class, 'generateAiProposal']);
Route::get('pricing-plans', [\App\Http\Controllers\API\PricingPlanApiController::class, 'index']);

// Case Studies
Route::get('case-studies', [\App\Http\Controllers\API\CaseStudyApiController::class, 'index']);
Route::get('case-studies/{id}', [\App\Http\Controllers\API\CaseStudyApiController::class, 'show']);

// Contact Form & AI Chatbot
Route::post('chat/bot', [\App\Http\Controllers\API\ChatApiController::class, 'chatBot']);
Route::post('contact', [\App\Http\Controllers\API\ContactApiController::class, 'store']);
Route::post('newsletter/subscribe', [\App\Http\Controllers\SubscriberController::class, 'store']);

// Bookings & Payments
Route::get('payments/stripe-key', [\App\Http\Controllers\API\PaymentApiController::class, 'getStripeKey']);
Route::get('payments/{booking}/invoice', [\App\Http\Controllers\API\PaymentApiController::class, 'downloadInvoice']);
Route::any('payments/webhook', [\App\Http\Controllers\API\PaymentApiController::class, 'webhook']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('campaign-bookings/create', [\App\Http\Controllers\API\CampaignBookingApiController::class, 'store']);
    Route::post('subscription-bookings/create', [\App\Http\Controllers\API\SubscriptionBookingApiController::class, 'store']);
    Route::post('subscription-bookings/upgrade', [\App\Http\Controllers\API\SubscriptionBookingApiController::class, 'upgrade']);
    Route::prefix('payments')->group(function () {
        Route::get('list', [\App\Http\Controllers\API\PaymentApiController::class, 'index']);
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('me', [\App\Http\Controllers\API\ProfileApiController::class, 'show']);
    Route::post('auth/logout', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'logoutApi']);
    // Profile Actions
    Route::get('profile', [\App\Http\Controllers\API\ProfileApiController::class, 'show']);
    Route::post('profile/update', [\App\Http\Controllers\API\ProfileApiController::class, 'update']);
    Route::post('profile/setup', [\App\Http\Controllers\API\ProfileApiController::class, 'setupProfile']);
    Route::post('profile/update-password', [\App\Http\Controllers\API\ProfileApiController::class, 'updatePassword']);
    // User Dashboard
    Route::get('user/dashboard', [\App\Http\Controllers\API\UserDashboardApiController::class, 'index']);
    Route::get('user/dashboard/ai-insights', [\App\Http\Controllers\API\UserDashboardApiController::class, 'getAiInsights']);
    // User Services & Bookings & Tasks
    Route::get('user/services', [\App\Http\Controllers\API\UserServicesApiController::class, 'index']);
    Route::get('user/reports', [\App\Http\Controllers\API\UserReportsApiController::class, 'index']);
    Route::get('user/reports/ai-summary', [\App\Http\Controllers\API\UserReportsApiController::class, 'getAiReportSummary']);
    Route::get('user/tasks', [\App\Http\Controllers\API\UserTasksApiController::class, 'index']);
    Route::post('tasks/{task}/update-progress', [\App\Http\Controllers\API\UserTasksApiController::class, 'updateProgress']);
    Route::get('user/bookings', [\App\Http\Controllers\API\UserBookingApiController::class, 'index']);
    Route::get('user/bookings/{id}', [\App\Http\Controllers\API\UserBookingApiController::class, 'show']);

    // Chat & Conversations
    Route::get('conversations', [\App\Http\Controllers\API\ChatApiController::class, 'getConversations']);
    Route::get('conversations/{id}/messages', [\App\Http\Controllers\API\ChatApiController::class, 'getMessages']);
    Route::post('conversations/send', [\App\Http\Controllers\API\ChatApiController::class, 'sendMessage']);

    // User Search
    Route::get('users/search', function (\Illuminate\Http\Request $request) {
        $query = $request->get('q', '');
        $currentUserId = \Illuminate\Support\Facades\Auth::id();
        $users = \App\Models\User::where('id', '!=', $currentUserId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'avatar', 'is_online', 'is_admin')
            ->limit(20)
            ->get();
        return response()->json(['status' => 'success', 'data' => $users]);
    });
});
