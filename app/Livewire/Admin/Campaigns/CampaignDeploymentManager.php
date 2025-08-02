<?php

namespace App\Livewire\Admin\Campaigns;

use App\Services\CampaignDeploymentExecutorService;
use App\Services\CampaignDeploymentService;
use App\Models\Campaign;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CampaignDeploymentManager extends Component
{
    public $selectedCampaigns = [];
    public $deploymentInProgress = false;
    public $lastDeploymentResult = null;
    public $deploymentStats = [];
    
    protected CampaignDeploymentService $deploymentService;
    protected CampaignDeploymentExecutorService $deploymentExecutor;

    public function boot(
        CampaignDeploymentService $deploymentService,
        CampaignDeploymentExecutorService $deploymentExecutor
    ) {
        $this->deploymentService = $deploymentService;
        $this->deploymentExecutor = $deploymentExecutor;
    }

    public function mount()
    {
        $this->loadDeploymentStats();
    }

    public function loadDeploymentStats()
    {
        $this->deploymentStats = $this->deploymentExecutor->getDeploymentStats();
    }

    /**
     * Deploy all ready campaigns
     */
    public function deployAllReady()
    {
        $this->deploymentInProgress = true;
        
        try {
            $result = $this->deploymentExecutor->deployAutomatically();
            
            $this->lastDeploymentResult = $result;
            
            if ($result['success']) {
                session()->flash('success', $result['message']);
            } else {
                session()->flash('error', $result['message']);
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Deployment failed: ' . $e->getMessage());
        } finally {
            $this->deploymentInProgress = false;
            $this->loadDeploymentStats();
        }
    }

    /**
     * Deploy selected campaigns
     */
    public function deploySelected()
    {
        if (empty($this->selectedCampaigns)) {
            session()->flash('error', 'Please select campaigns to deploy');
            return;
        }

        $this->deploymentInProgress = true;
        
        try {
            $result = $this->deploymentExecutor->deployManually($this->selectedCampaigns);
            
            $this->lastDeploymentResult = $result;
            
            if ($result['success']) {
                session()->flash('success', $result['message']);
                $this->selectedCampaigns = []; // Clear selection
            } else {
                session()->flash('error', $result['message']);
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Deployment failed: ' . $e->getMessage());
        } finally {
            $this->deploymentInProgress = false;
            $this->loadDeploymentStats();
        }
    }

    /**
     * Validate selected campaigns
     */
    public function validateSelected()
    {
        if (empty($this->selectedCampaigns)) {
            session()->flash('error', 'Please select campaigns to validate');
            return;
        }

        $validation = $this->deploymentService->validateCampaignsForDeployment($this->selectedCampaigns);
        
        if ($validation['invalid_count'] > 0) {
            $errorMessage = "Validation failed for {$validation['invalid_count']} campaigns. Check campaign configuration.";
            session()->flash('error', $errorMessage);
        } else {
            session()->flash('success', "All {$validation['valid_count']} selected campaigns passed validation!");
        }
        
        $this->lastDeploymentResult = $validation;
    }

    public $deployments = [];

    /**
     * Redeploy a campaign deployment by ID.
     */
    public function redeploy($deploymentId)
    {
        $deployment = \App\Models\CampaignDeployment::find($deploymentId);
        if ($deployment) {
            // Trigger redeployment logic (reuse deploySelected or custom logic)
            // For now, just dispatch the job again
            \App\Jobs\DeployCampaignToWebsiteJob::dispatch($deployment->campaign_id, $deployment->campaign->campaignWebsites->first()->website_id ?? null);
            session()->flash('success', 'Redeployment triggered.');
        } else {
            session()->flash('error', 'Deployment not found.');
        }
        $this->loadDeployments();
    }

    /**
     * Load recent deployments for display.
     */
    public function loadDeployments()
    {
        $this->deployments = 
            \App\Models\CampaignDeployment::with('campaign')
                ->orderByDesc('deployed_at')
                ->limit(20)
                ->get();
    }

    public function render()
    {
        $deployableCampaigns = $this->deploymentService->prepareCampaignsForDeployment();
        $allCampaigns = Campaign::with(['campaignWebsites', 'campaignTriggers'])
            ->whereIn('status', ['active', 'scheduled', 'disabled'])
            ->orderBy('priority', 'desc')
            ->get();
        $this->loadDeployments();
        return view('livewire.admin.campaigns.campaign-deployment-manager', [
            'deployableCampaigns' => $deployableCampaigns,
            'allCampaigns' => $allCampaigns,
            'deployments' => $this->deployments,
        ]);
    }
}