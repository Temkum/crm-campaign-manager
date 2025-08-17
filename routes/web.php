<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Campaigns\CampaignsIndex;
use App\Livewire\Admin\Campaigns\CampaignManager;
use App\Http\Controllers\Api\CampaignDeploymentController;
use App\Livewire\Admin\Campaigns\CampaignDeploymentManager;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\DashboardOverview;
use App\Livewire\Admin\Websites\WebsitesComponent;
use App\Livewire\Admin\Websites\AddWebsite;
use App\Livewire\Admin\Websites\EditWebsite;
use App\Livewire\Admin\Markets\MarketsIndex;
use App\Livewire\Admin\Markets\AddMarket;
use App\Livewire\Admin\Markets\EditMarket;
use App\Livewire\Admin\Operators\OperatorsIndex;
use App\Livewire\Admin\Operators\AddOperator;
use App\Livewire\Admin\Operators\EditOperator;

Route::view('/', 'welcome');

Route::get('dashboard', Dashboard::class)
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
        Route::get('/websites', WebsitesComponent::class)->name('websites.index');
        Route::get('/websites/create', AddWebsite::class)->name('websites.create');
        Route::get('/websites/{website}/edit', EditWebsite::class)->name('websites.edit');

        // Campaign deployment routes
        Route::post('/campaigns/deployment/deploy', [CampaignDeploymentController::class, 'deploy'])
            ->name('campaigns.deployment.deploy');
        Route::post('/campaigns/deployment/deploy-all', [CampaignDeploymentController::class, 'deployAll'])
            ->name('campaigns.deployment.deploy-all');
        Route::get('/campaigns/deployment/stats', [CampaignDeploymentController::class, 'stats'])
            ->name('campaigns.deployment.stats');
        Route::post('/campaigns/deployment/validate', [CampaignDeploymentController::class, 'validate'])
            ->name('campaigns.deployment.validate');

        // Markets routes
        Route::get('/markets', MarketsIndex::class)->name('markets.index');
        Route::get('/markets/create', AddMarket::class)->name('markets.create');
        Route::get('/markets/{market}/edit', EditMarket::class)->name('markets.edit');

        // Operators routes
        Route::get('/operators', OperatorsIndex::class)->name('operators.index');
        Route::get('/operators/create', AddOperator::class)->name('operators.create');
        Route::get('/operators/{operator}/edit', EditOperator::class)->name('operators.edit');
    });

    Route::get('/health', function () {
        return response()->json(['status' => 'ok']);
    });
});

require __DIR__ . '/auth.php';

// Fallback route for unmatched URIs -> show custom 404 page
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});