<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Campaigns\CampaignsIndex;
use App\Livewire\Admin\Campaigns\CampaignManager;
use App\Http\Controllers\Api\CampaignDeploymentController;
use App\Livewire\Admin\Campaigns\CampaignDeploymentManager;

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
        Route::get('/{campaign_id}/edit', CampaignManager::class)->name('edit');
    });

    Route::prefix('campaigns/deployment')->group(function () {
        Route::get('/', CampaignDeploymentManager::class)->name('campaigns.deployments');
        Route::get('/website', [CampaignDeploymentController::class, 'getCampaignsForWebsite']);
        Route::post('/deploy', [CampaignDeploymentController::class, 'deploySpecificCampaigns']);
        Route::get('/stats', [CampaignDeploymentController::class, 'getDeploymentStats']);
        Route::post('/validate', [CampaignDeploymentController::class, 'validateCampaigns']);
    });

    Route::middleware(['auth'])->prefix('admin')->group(function () {
        // Websites routes
        Route::get('/websites', \App\Livewire\Admin\Websites\WebsitesComponent::class)->name('websites.index');
        Route::get('/websites/create', \App\Livewire\Admin\Websites\AddWebsite::class)->name('websites.create');
        Route::get('/websites/{website}/edit', \App\Livewire\Admin\Websites\EditWebsite::class)->name('websites.edit');

        Route::post('/campaigns/deployment/deploy', [CampaignDeploymentController::class, 'deploy'])
            ->name('campaigns.deployment.deploy');

        Route::post('/campaigns/deployment/deploy-all', [CampaignDeploymentController::class, 'deployAll'])
            ->name('campaigns.deployment.deploy-all');

        Route::get('/campaigns/deployment/stats', [CampaignDeploymentController::class, 'stats'])
            ->name('campaigns.deployment.stats');

        Route::post('/campaigns/deployment/validate', [CampaignDeploymentController::class, 'validate'])
            ->name('campaigns.deployment.validate');
    });
});

require __DIR__ . '/auth.php';
