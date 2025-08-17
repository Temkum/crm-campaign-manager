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
    public $selectAll = false; // Added to support wire:model="selectAll"
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
        try {
            $this->deploymentStats = $this->deploymentExecutor->getDeploymentStats();
        } catch (\Throwable $e) {
            $this->deploymentStats = [
                'today_deployments' => 0,
                'pending_queue_jobs' => 0,
                'recent_failures' => 0,
                'active_campaigns' => 0,
            ];
            session()->flash('error', 'Could not load deployment stats.');
        }
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
                session()->flash('success', $result['message'] ?? 'Deployment queued.');
            } else {
                session()->flash('error', $result['message'] ?? 'Deployment failed.');
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
                session()->flash('success', $result['message'] ?? 'Deployment queued.');
                $this->selectedCampaigns = []; // Clear selection
                $this->selectAll = false;
            } else {
                session()->flash('error', $result['message'] ?? 'Deployment failed.');
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

        try {
            $validation = $this->deploymentService->validateCampaignsForDeployment($this->selectedCampaigns);

            if (($validation['invalid_count'] ?? 0) > 0) {
                $errorMessage = "Validation failed for " . ($validation['invalid_count'] ?? 0) . " campaigns. Check campaign configuration.";
                session()->flash('error', $errorMessage);
            } else {
                session()->flash('success', "All " . ($validation['valid_count'] ?? 0) . " selected campaigns passed validation!");
            }

            $this->lastDeploymentResult = $validation;
        } catch (\Throwable $e) {
            session()->flash('error', 'Validation failed: ' . $e->getMessage());
        }
    }

    public $deployments = [];

    /**
     * Redeploy a campaign deployment by ID.
     */
    public function redeploy($deploymentId)
    {
        try {
            $deployment = \App\Models\CampaignDeployment::with('campaign.campaignWebsites')
                ->find($deploymentId);

            if (!$deployment || !$deployment->campaign) {
                session()->flash('error', 'Deployment not found.');
                return;
            }

            $campaignModel = $deployment->campaign;
            // Prefer website_id from previous deployment metadata; fallback to first configured website
            $websiteId = data_get($deployment->metadata, 'context.website_id')
                ?? optional($campaignModel->campaignWebsites->first())->website_id;

            if (!$websiteId) {
                session()->flash('error', 'No target website found for redeploy.');
                return;
            }

            // Build website-specific campaign payload via service
            $prepared = $this->deploymentService->prepareCampaignsForWebsiteDeployment($websiteId);
            $campaignPayload = collect($prepared)->firstWhere('id', $campaignModel->id) ?? [
                'id' => $campaignModel->id,
                'name' => $campaignModel->name,
                'status' => $campaignModel->status,
                'priority' => $campaignModel->priority,
                'start_at' => optional($campaignModel->start_at)->toISOString(),
                'end_at' => optional($campaignModel->end_at)->toISOString(),
            ];

            \App\Jobs\DeployCampaignToWebsiteJob::dispatch($campaignPayload, (int) $websiteId);
            session()->flash('success', 'Redeployment triggered.');
        } catch (\Throwable $e) {
            session()->flash('error', 'Redeploy failed: ' . $e->getMessage());
        }

        $this->loadDeployments();
    }

    /**
     * Load recent deployments for display.
     */
    public function loadDeployments()
    {
        try {
            $this->deployments =
                \App\Models\CampaignDeployment::with('campaign')
                ->orderByDesc('deployed_at')
                ->limit(20)
                ->get();
        } catch (\Throwable $e) {
            $this->deployments = collect();
            session()->flash('error', 'Could not load recent deployments.');
        }
    }

    /**
     * Toggle select all campaigns in the table.
     */
    public function updatedSelectAll($value)
    {
        try {
            if ($value) {
                $this->selectedCampaigns = Campaign::whereIn('status', ['active', 'scheduled', 'disabled'])
                    ->orderBy('priority', 'desc')
                    ->pluck('id')
                    ->toArray();
            } else {
                $this->selectedCampaigns = [];
            }
        } catch (\Throwable $e) {
            $this->selectedCampaigns = [];
            $this->selectAll = false;
            session()->flash('error', 'Could not update selection.');
        }
    }

    public function render()
    {
        try {
            $deployableCampaigns = $this->deploymentService->prepareCampaignsForDeployment();
        } catch (\Throwable $e) {
            $deployableCampaigns = [];
            session()->flash('error', 'Failed to prepare deployable campaigns.');
        }

        try {
            $allCampaigns = Campaign::with(['campaignWebsites', 'campaignTriggers'])
                ->whereIn('status', ['active', 'scheduled', 'disabled'])
                ->orderBy('priority', 'desc')
                ->get();
        } catch (\Throwable $e) {
            $allCampaigns = collect();
            session()->flash('error', 'Failed to load campaigns.');
        }

        $this->loadDeployments();

        return view('livewire.admin.campaigns.campaign-deployment-manager', [
            'deployableCampaigns' => $deployableCampaigns,
            'allCampaigns' => $allCampaigns,
            'deployments' => $this->deployments,
        ]);
    }
}