<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\CreatorProfileController;
use App\Http\Controllers\Api\CreatorSupportController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PublicCampaignController;
use App\Http\Controllers\Api\PledgeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| SPA-friendly API using session cookies (same-origin) + CSRF.
| We wrap routes with the `web` middleware so Laravel sessions/CSRF work.
|
*/

Route::middleware(['web'])->group(function () {
    // Auth (session-based)
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/login', [AuthController::class, 'login'])->middleware(['guest', 'throttle:20,1']);
    Route::post('/register', [AuthController::class, 'register'])->middleware(['guest', 'throttle:10,1']);
    Route::get('/oauth/google/redirect', [AuthController::class, 'redirectToGoogle'])->middleware(['guest', 'throttle:30,1']);
    Route::get('/oauth/google/callback', [AuthController::class, 'handleGoogleCallback'])->middleware(['guest', 'throttle:30,1']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

    // Public campaigns
    Route::get('/campaigns', [PublicCampaignController::class, 'index']);
    Route::get('/campaigns/{slug}', [PublicCampaignController::class, 'show']);

    // Support a creator ("seguir" / "apoiar")
    Route::get('/creators/{creator}/support', [CreatorSupportController::class, 'show']);

    // Creator/dashboard
    Route::middleware('auth')->group(function () {
        // Creator onboarding/profile
        Route::get('/me/creator-profile', [CreatorProfileController::class, 'show']);
        Route::post('/me/creator-profile', [CreatorProfileController::class, 'store']);

        // In-app notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

        Route::post('/creators/{creator}/support', [CreatorSupportController::class, 'store']);
        Route::delete('/creators/{creator}/support', [CreatorSupportController::class, 'destroy']);

        Route::get('/dashboard/campaigns', [DashboardController::class, 'index']);
        Route::get('/dashboard/campaigns/{id}', [DashboardController::class, 'show']);

        Route::get('/me/campaigns/{id}', [CampaignController::class, 'show']);
        Route::post('/me/campaigns', [CampaignController::class, 'store']);
        Route::put('/me/campaigns/{id}', [CampaignController::class, 'update']);
        Route::post('/me/campaigns/{id}/publish', [CampaignController::class, 'publish']);

        Route::post('/pledges', [PledgeController::class, 'store'])->middleware('throttle:10,1');
    });
});
