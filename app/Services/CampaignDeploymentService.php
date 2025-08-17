<?php

namespace App\Services;

use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CampaignDeploymentService
{
    /**
     * Prepare campaigns for deployment by transforming them into standardized format
     * 
     * @param array|null $campaignIds Optional array of specific campaign IDs to deploy
     * @return array Array of standardized campaign objects ready for deployment
     */
    public function prepareCampaignsForDeployment(?array $campaignIds = null): array
    {
        $query = Campaign::with(['campaignTriggers', 'campaignWebsites'])
            ->whereIn('status', ['active', 'scheduled']) // Only deployable campaigns
            ->where('start_at', '<=', now()) // Started campaigns
            ->where(function ($q) {
                $q->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            }); // Not yet ended campaigns or no end date

        // If specific campaign IDs are provided, filter by them
        if ($campaignIds) {
            $query->whereIn('id', $campaignIds);
        }

        $campaigns = $query->orderBy('priority', 'desc')
            ->orderBy('start_at', 'asc')
            ->get();

        return $this->transformCampaignsToDeploymentFormat($campaigns);
    }

    /**
     * Prepare specific campaigns for deployment regardless of status
     * 
     * @param array $campaignIds Array of campaign IDs to deploy
     * @return array Array of standardized campaign objects
     */
    public function prepareCampaignsForForcedDeployment(array $campaignIds): array
    {
        $campaigns = Campaign::with(['campaignTriggers', 'campaignWebsites'])
            ->whereIn('id', $campaignIds)
            ->orderBy('priority', 'desc')
            ->orderBy('start_at', 'asc')
            ->get();

        return $this->transformCampaignsToDeploymentFormat($campaigns);
    }

    /**
     * Get campaigns ready for deployment with website-specific data
     * 
     * @param int|null $websiteId Optional website ID to filter campaigns
     * @return array Array of campaigns with website-specific configuration
     */
    public function prepareCampaignsForWebsiteDeployment(?int $websiteId = null): array
    {
        $query = Campaign::with(['campaignTriggers', 'campaignWebsites.website'])
            ->whereIn('status', ['active', 'scheduled'])
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());

        // Filter by specific website if provided
        if ($websiteId) {
            $query->whereHas('campaignWebsites', function ($q) use ($websiteId) {
                $q->where('website_id', $websiteId);
            });
        }

        $campaigns = $query->orderBy('priority', 'desc')
            ->orderBy('start_at', 'asc')
            ->get();

        return $this->transformCampaignsToWebsiteDeploymentFormat($campaigns, $websiteId);
    }

    /**
     * Transform campaigns collection to standardized deployment format
     * 
     * @param Collection $campaigns
     * @return array
     */
    protected function transformCampaignsToDeploymentFormat(Collection $campaigns): array
    {
        return $campaigns->map(function (Campaign $campaign) {
            // Get the primary website configuration (highest priority)
            $primaryWebsite = $campaign->campaignWebsites
                ->sortByDesc('priority')
                ->first();

            return [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'start_at' => $this->formatTimestamp($campaign->start_at),
                'end_at' => $this->formatTimestamp($campaign->end_at),
                'status' => $campaign->status,
                'priority' => $campaign->priority,
                'dom_selector' => $campaign->dom_selector ?: ($primaryWebsite?->dom_selector ?? null),
                'custom_affiliate_url' => $primaryWebsite?->custom_affiliate_url ?? null,
                'timer_offset' => $primaryWebsite?->timer_offset ?? $campaign->duration ?? null,
                'triggers' => $this->transformTriggersForDeployment($campaign->campaignTriggers),
            ];
        })->toArray();
    }

    /**
     * Transform campaigns to website-specific deployment format
     * 
     * @param Collection $campaigns
     * @param int|null $websiteId
     * @return array
     */
    protected function transformCampaignsToWebsiteDeploymentFormat(Collection $campaigns, ?int $websiteId = null): array
    {
        return $campaigns->map(function (Campaign $campaign) use ($websiteId) {
            // Get website-specific configuration
            $websiteConfig = null;
            if ($websiteId) {
                $websiteConfig = $campaign->campaignWebsites
                    ->where('website_id', $websiteId)
                    ->first();
            } else {
                // Get highest priority website config
                $websiteConfig = $campaign->campaignWebsites
                    ->sortByDesc('priority')
                    ->first();
            }

            return [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'start_at' => $this->formatTimestamp($campaign->start_at),
                'end_at' => $this->formatTimestamp($campaign->end_at),
                'status' => $campaign->status,
                'priority' => $websiteConfig?->priority ?? $campaign->priority,
                'dom_selector' => $websiteConfig?->dom_selector ?: $campaign->dom_selector,
                'custom_affiliate_url' => $websiteConfig?->custom_affiliate_url ?? null,
                'timer_offset' => $websiteConfig?->timer_offset ?? $campaign->duration ?? null,
                'website_id' => $websiteConfig?->website_id ?? null,
                'triggers' => $this->transformTriggersForDeployment($campaign->campaignTriggers),
            ];
        })->toArray();
    }

    /**
     * Transform campaign triggers to deployment format
     * 
     * @param Collection $triggers
     * @return array
     */
    protected function transformTriggersForDeployment(Collection $triggers): array
    {
        return $triggers->map(function ($trigger) {
            return [
                'id' => $trigger->id,
                'type' => $trigger->type,
                'value' => $trigger->value,
                'operator' => $trigger->operator ?? null, // Include operator if needed
            ];
        })->toArray();
    }

    /**
     * Format timestamp to ISO 8601 format with microseconds
     * 
     * @param Carbon|null $timestamp
     * @return string|null
     */
    protected function formatTimestamp(?Carbon $timestamp): ?string
    {
        return $timestamp ? $timestamp->toISOString() : null;
    }

    /**
     * Get deployment statistics
     * 
     * @return array
     */
    public function getDeploymentStatistics(): array
    {
        $totalCampaigns = Campaign::count();
        $activeCampaigns = Campaign::where('status', 'active')->count();
        $scheduledCampaigns = Campaign::where('status', 'scheduled')->count();
        $deployableCampaigns = Campaign::whereIn('status', ['active', 'scheduled'])
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->count();

        return [
            'total_campaigns' => $totalCampaigns,
            'active_campaigns' => $activeCampaigns,
            'scheduled_campaigns' => $scheduledCampaigns,
            'deployable_campaigns' => $deployableCampaigns,
            'deployment_ready' => $deployableCampaigns > 0,
        ];
    }

    /**
     * Validate campaigns before deployment
     * 
     * @param array $campaignIds
     * @return array Validation results with any issues found
     */
    public function validateCampaignsForDeployment(array $campaignIds): array
    {
        $campaigns = Campaign::with(['campaignTriggers', 'campaignWebsites'])
            ->whereIn('id', $campaignIds)
            ->get();

        $issues = [];
        $validCampaigns = [];

        foreach ($campaigns as $campaign) {
            $campaignIssues = [];

            // Check if campaign has triggers
            if ($campaign->campaignTriggers->isEmpty()) {
                $campaignIssues[] = 'No triggers configured';
            }

            // Check if campaign has websites
            if ($campaign->campaignWebsites->isEmpty()) {
                $campaignIssues[] = 'No websites configured';
            }

            // Check if campaign dates are valid
            if ($campaign->start_at && $campaign->end_at && $campaign->start_at >= $campaign->end_at) {
                $campaignIssues[] = 'Invalid date range (start date must be before end date)';
            }

            // Check if campaign has ended
            if ($campaign->end_at && $campaign->end_at < now()) {
                $campaignIssues[] = 'Campaign has already ended';
            }

            if (empty($campaignIssues)) {
                $validCampaigns[] = $campaign->id;
            } else {
                $issues[$campaign->id] = [
                    'campaign_name' => $campaign->name,
                    'issues' => $campaignIssues,
                ];
            }
        }

        return [
            'valid_campaigns' => $validCampaigns,
            'invalid_campaigns' => $issues,
            'total_validated' => count($campaignIds),
            'valid_count' => count($validCampaigns),
            'invalid_count' => count($issues),
        ];
    }
}
