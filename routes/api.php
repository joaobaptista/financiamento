<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\DashboardController;
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
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
    Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

    // Public campaigns
    Route::get('/campaigns', [PublicCampaignController::class, 'index']);
    Route::get('/campaigns/{slug}', [PublicCampaignController::class, 'show']);

    // Creator/dashboard
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard/campaigns', [DashboardController::class, 'index']);
        Route::get('/dashboard/campaigns/{id}', [DashboardController::class, 'show']);

        Route::get('/me/campaigns/{id}', [CampaignController::class, 'show']);
        Route::post('/me/campaigns', [CampaignController::class, 'store']);
        Route::put('/me/campaigns/{id}', [CampaignController::class, 'update']);
        Route::post('/me/campaigns/{id}/publish', [CampaignController::class, 'publish']);

        Route::post('/pledges', [PledgeController::class, 'store']);
    });
});
