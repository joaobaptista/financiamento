<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// SPA entrypoints (GET) — serve Vue for all screens
Route::view('/', 'spa')->name('home');

Route::view('/campaigns', 'spa')->name('campaigns.index');
Route::view('/campaigns/{slug}', 'spa')->name('campaigns.show');

Route::middleware(['auth'])->group(function () {
	Route::view('/dashboard', 'spa')->name('dashboard.index');
	Route::view('/dashboard/campaigns/{id}', 'spa')->name('dashboard.show');

	Route::view('/me/campaigns/create', 'spa')->name('campaigns.create');
	Route::view('/me/campaigns/{id}/edit', 'spa')->name('campaigns.edit');

	Route::view('/profile', 'spa')->name('profile.edit');

	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// Fallback for Vue Router
Route::view('/{any}', 'spa')->where('any', '.*');

