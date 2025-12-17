<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PledgeController;
use Illuminate\Support\Facades\Route;

// Página inicial
Route::get('/', function () {
    return view('home');
})->name('home');

// Rotas públicas de campanhas
Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
Route::get('/campaigns/{slug}', [CampaignController::class, 'show'])->name('campaigns.show');

// Dashboard do criador (autenticado)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/campaigns/{id}', [DashboardController::class, 'show'])->name('dashboard.show');

    // CRUD de campanhas
    Route::get('/me/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create');
    Route::post('/me/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::get('/me/campaigns/{id}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit');
    Route::put('/me/campaigns/{id}', [CampaignController::class, 'update'])->name('campaigns.update');
    Route::post('/me/campaigns/{id}/publish', [CampaignController::class, 'publish'])->name('campaigns.publish');

    // Apoios
    Route::post('/pledges', [PledgeController::class, 'store'])->name('pledges.store');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

