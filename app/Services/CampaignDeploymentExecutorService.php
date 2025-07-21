<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Website;
use App\Models\CampaignDeployment;
use App\Jobs\DeployCampaignJob;
use App\Jobs\DeployCampaignToWebsiteJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Carbon\Carbon;

class CampaignDeploymentExecutorService
{
    protected CampaignDeploymentService $deploymentService;

    public function __construct(CampaignDeploymentService $deploymentService)
    {
        $this->deploymentService = $deploymentService;
    }

    /**
     * Manual deployment - triggered by button click
     * 
     * @param array $campaignIds
     * @param bool $useQueue Whether to use queue for deployment
     * @return array
     */
    public function deployManually(array $campaignIds, bool $useQueue = true): array
    {
        // Validate campaigns first
        $validation = $this->deploymentService->validateCampaignsForDeployment($campaignIds);

        if ($validation['invalid_count'] > 0) {
            return [
                'success' => false,
                'message' => 'Some campaigns failed validation',
                'validation' => $validation,
            ];
        }

        // Get deployment-ready campaigns
        $campaigns = $this->deploymentService->prepareCampaignsForForcedDeployment($campaignIds);

        if ($useQueue) {
            // Dispatch to queue for async processing
            foreach ($campaigns as $campaign) {
                DeployCampaignJob::dispatch($campaign)
                    ->onQueue('campaign-deployment');
            }

            return [
                'success' => true,
                'message' => 'Campaigns queued for deployment',
                'campaign_count' => count($campaigns),
                'deployment_method' => 'queued',
            ];
        } else {
            // Deploy synchronously
            $results = [];
            foreach ($campaigns as $campaign) {
                $results[] = $this->deployToAllWebsites($campaign);
            }

            return [
                'success' => true,
                'message' => 'Campaigns deployed successfully',
                'results' => $results,
                'deployment_method' => 'synchronous',
            ];
        }
    }

    /**
     * Automatic deployment - for cron/scheduled jobs
     * 
     * @return array
     */
    public function deployAutomatically(): array
    {
        // Get all campaigns ready for deployment
        $campaigns = $this->deploymentService->prepareCampaignsForDeployment();

        if (empty($campaigns)) {
            return [
                'success' => true,
                'message' => 'No campaigns ready for deployment',
                'campaign_count' => 0,
            ];
        }

        $deployed = 0;
        $failed = 0;
        $errors = [];

        foreach ($campaigns as $campaign) {
            try {
                // Check if already deployed recently (prevent duplicate deployments)
                if ($this->isRecentlyDeployed($campaign['id'])) {
                    continue;
                }

                // Queue deployment job
                DeployCampaignJob::dispatch($campaign)
                    ->onQueue('campaign-deployment');

                $deployed++;

                // Log deployment attempt
                $this->logDeploymentAttempt($campaign['id'], 'queued');
            } catch (\Exception $e) {
                $failed++;
                $errors[] = [
                    'campaign_id' => $campaign['id'],
                    'error' => $e->getMessage(),
                ];

                Log::error("Failed to queue campaign {$campaign['id']} for deployment", [
                    'error' => $e->getMessage(),
                    'campaign' => $campaign,
                ]);
            }
        }

        return [
            'success' => $failed === 0,
            'message' => "Deployed: {$deployed}, Failed: {$failed}",
            'deployed_count' => $deployed,
            'failed_count' => $failed,
            'errors' => $errors,
        ];
    }

    /**
     * Deploy campaign to all associated websites
     * 
     * @param array $campaign
     * @return array
     */
    public function deployToAllWebsites(array $campaign): array
    {
        $campaignModel = Campaign::with('campaignWebsites.website')->find($campaign['id']);

        if (!$campaignModel) {
            return [
                'success' => false,
                'campaign_id' => $campaign['id'],
                'message' => 'Campaign not found',
            ];
        }

        $results = [];

        foreach ($campaignModel->campaignWebsites as $websiteConfig) {
            try {
                // Get website-specific campaign data
                $websiteCampaign = $this->deploymentService->prepareCampaignsForWebsiteDeployment(
                    $websiteConfig->website_id
                );

                // Filter for current campaign
                $campaignData = collect($websiteCampaign)
                    ->where('id', $campaign['id'])
                    ->first();

                if ($campaignData) {
                    $result = $this->deployToWebsite($campaignData, $websiteConfig->website);
                    $results[] = $result;
                }
            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'website_id' => $websiteConfig->website_id,
                    'website_url' => $websiteConfig->website->url ?? 'Unknown',
                    'error' => $e->getMessage(),
                ];

                Log::error("Failed to deploy campaign {$campaign['id']} to website {$websiteConfig->website_id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Log overall deployment result
        $this->logDeploymentResult($campaign['id'], $results);

        return [
            'success' => !collect($results)->contains('success', false),
            'campaign_id' => $campaign['id'],
            'campaign_name' => $campaign['name'],
            'website_results' => $results,
        ];
    }

    /**
     * Deploy campaign to specific website
     * 
     * @param array $campaign
     * @param Website $website
     * @return array
     */
    public function deployToWebsite(array $campaign, Website $website): array
    {
        try {
            // Determine deployment method based on website type
            switch ($website->type) {
                case 'api':
                    return $this->deployViaApi($campaign, $website);
                case 'webhook':
                    return $this->deployViaWebhook($campaign, $website);
                case 'file':
                    return $this->deployViaFile($campaign, $website);
                default:
                    return $this->deployViaApi($campaign, $website); // Default to API
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'website_id' => $website->id,
                'website_url' => $website->url,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Deploy via API call
     * 
     * @param array $campaign
     * @param Website $website
     * @return array
     */
    protected function deployViaApi(array $campaign, Website $website): array
    {
        $endpoint = $website->deployment_endpoint ?? $website->url . '/api/campaigns';

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . ($website->api_token ?? ''),
                'Content-Type' => 'application/json',
            ])
            ->post($endpoint, [
                'campaign' => $campaign,
                'deployment_timestamp' => now()->toISOString(),
                'source' => 'campaign_manager',
            ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'website_id' => $website->id,
                'website_url' => $website->url,
                'method' => 'api',
                'response_data' => $response->json(),
            ];
        } else {
            throw new \Exception("API deployment failed: {$response->status()} - {$response->body()}");
        }
    }

    /**
     * Deploy via webhook
     * 
     * @param array $campaign
     * @param Website $website
     * @return array
     */
    protected function deployViaWebhook(array $campaign, Website $website): array
    {
        $webhookUrl = $website->webhook_url;

        if (!$webhookUrl) {
            throw new \Exception('Webhook URL not configured for website');
        }

        $response = Http::timeout(30)
            ->post($webhookUrl, [
                'event' => 'campaign.deployed',
                'data' => $campaign,
                'website_id' => $website->id,
                'timestamp' => now()->toISOString(),
            ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'website_id' => $website->id,
                'website_url' => $website->url,
                'method' => 'webhook',
                'webhook_response' => $response->json(),
            ];
        } else {
            throw new \Exception("Webhook deployment failed: {$response->status()}");
        }
    }

    /**
     * Deploy via file generation
     * 
     * @param array $campaign
     * @param Website $website
     * @return array
     */
    protected function deployViaFile(array $campaign, Website $website): array
    {
        $fileName = "campaign_{$campaign['id']}_website_{$website->id}.json";
        $filePath = storage_path("app/deployments/{$fileName}");

        // Ensure directory exists
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        // Write campaign data to file
        file_put_contents($filePath, json_encode($campaign, JSON_PRETTY_PRINT));

        return [
            'success' => true,
            'website_id' => $website->id,
            'website_url' => $website->url,
            'method' => 'file',
            'file_path' => $filePath,
        ];
    }

    /**
     * Check if campaign was recently deployed (within last hour)
     * 
     * @param int $campaignId
     * @return bool
     */
    protected function isRecentlyDeployed(int $campaignId): bool
    {
        return CampaignDeployment::where('campaign_id', $campaignId)
            ->where('deployed_at', '>', now()->subHour())
            ->exists();
    }

    /**
     * Log deployment attempt
     * 
     * @param int $campaignId
     * @param string $status
     */
    protected function logDeploymentAttempt(int $campaignId, string $status): void
    {
        CampaignDeployment::create([
            'campaign_id' => $campaignId,
            'status' => $status,
            'deployed_at' => now(),
            'metadata' => [
                'deployment_method' => 'automatic',
                'triggered_by' => 'system',
            ],
        ]);
    }

    /**
     * Log deployment result
     * 
     * @param int $campaignId
     * @param array $results
     */
    protected function logDeploymentResult(int $campaignId, array $results): void
    {
        $successful = collect($results)->where('success', true)->count();
        $failed = collect($results)->where('success', false)->count();

        CampaignDeployment::updateOrCreate(
            [
                'campaign_id' => $campaignId,
                'deployed_at' => now()->startOfMinute(), // Group by minute
            ],
            [
                'status' => $failed > 0 ? 'partial' : 'completed',
                'metadata' => [
                    'websites_successful' => $successful,
                    'websites_failed' => $failed,
                    'results' => $results,
                ],
            ]
        );
    }

    /**
     * Get deployment statistics
     * 
     * @return array
     */
    public function getDeploymentStats(): array
    {
        $today = now()->startOfDay();

        return [
            'today_deployments' => CampaignDeployment::where('deployed_at', '>=', $today)->count(),
            'pending_queue_jobs' => Queue::size('campaign-deployment'),
            'recent_failures' => CampaignDeployment::where('status', 'failed')
                ->where('deployed_at', '>=', now()->subHours(24))
                ->count(),
            'active_campaigns' => Campaign::where('status', 'active')->count(),
        ];
    }
}
