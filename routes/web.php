<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Campaigns\CampaignManager;
use App\Livewire\Admin\Campaigns\CampaignsIndex;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {

    // Campaign routes
    Route::prefix('campaigns')->name('campaigns.')->group(function () {
        Route::get('/', CampaignsIndex::class)->name('index');
        Route::get('/create', CampaignManager::class)->name('create');
        Route::get('/{campaign}/edit', CampaignManager::class)->name('edit');
    });
});

require __DIR__.'/auth.php';