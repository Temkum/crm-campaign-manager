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
    protected CloudflareKVService $cloudflareKV;

    public function __construct(
        CampaignDeploymentService $deploymentService,
        CloudflareKVService $cloudflareKV
    ) {
        $this->deploymentService = $deploymentService;
        $this->cloudflareKV = $cloudflareKV;
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
     * Updated to include Cloudflare KV deployment
     * 
     * @param array $campaign - Your campaign array from the job
     * @return array
     */
    public function deployToAllWebsites(array $campaign): array
    {
        Log::info("Starting deployment to all websites", [
            'campaign_id' => $campaign['id'],
            'campaign_name' => $campaign['name'],
            'deployment_method' => 'cloudflare_kv_primary'
        ]);

        // Log memory usage at start
        Log::debug('Memory usage at deployment start', [
            'memory_usage' => memory_get_usage(true) / 1024 / 1024 . ' MB',
            'peak_memory' => memory_get_peak_usage(true) / 1024 / 1024 . ' MB'
        ]);

        $results = [
            'success' => true,
            'campaign_id' => $campaign['id'],
            'campaign_name' => $campaign['name'],
            'deployed_sites' => [],
            'failed_sites' => [],
            'cloudflare_results' => [],
            'total_sites' => 0,
        ];

        try {
            // Get websites from your campaign relationships
            $websites = $this->getCampaignWebsites($campaign);
            $results['total_sites'] = count($websites);

            Log::info("Found websites for deployment", [
                'campaign_id' => $campaign['id'],
                'website_count' => count($websites)
            ]);

            foreach ($websites as $website) {
                $domain = $website['domain'];

                try {
                    // 1. Deploy to Cloudflare KV (Primary method)
                    Log::info("Preparing campaign data for Cloudflare KV", [
                        'campaign_id' => $campaign['id'],
                        'domain' => $domain,
                        'website_id' => $website['id']
                    ]);

                    $campaignData = $this->prepareCampaignDataForCloudflare($campaign, $website);

                    // Log the prepared data (excluding sensitive info)
                    $loggableData = $campaignData;
                    unset($loggableData['campaign_metadata']); // Remove potentially large metadata
                    Log::debug("Prepared campaign data for KV storage", [
                        'campaign_id' => $campaign['id'],
                        'domain' => $domain,
                        'data_summary' => [
                            'campaign_id' => $campaignData['id'],
                            'name' => $campaignData['name'],
                            'active' => $campaignData['active'] ?? false,
                            'priority' => $campaignData['priority'] ?? 1,
                            'triggers_count' => count($campaignData['triggers'] ?? [])
                        ]
                    ]);

                    Log::info("Storing campaign data in Cloudflare KV", [
                        'campaign_id' => $campaign['id'],
                        'domain' => $domain,
                        'kv_namespace_id' => config('services.cloudflare.kv_namespace_id')
                    ]);

                    $kvSuccess = $this->cloudflareKV->storeCampaignData($domain, $campaignData);

                    if ($kvSuccess) {
                        Log::info("Successfully stored campaign in Cloudflare KV, invalidating cache", [
                            'campaign_id' => $campaign['id'],
                            'domain' => $domain,
                            'kv_success' => true
                        ]);

                        // Invalidate cache for immediate updates
                        $cacheResult = $this->cloudflareKV->invalidateCache($domain);
                        Log::info("Cache invalidation result", [
                            'campaign_id' => $campaign['id'],
                            'domain' => $domain,
                            'cache_invalidated' => $cacheResult
                        ]);

                        $results['deployed_sites'][] = [
                            'domain' => $domain,
                            'website_id' => $website['id'],
                            'status' => 'success',
                            'method' => 'cloudflare_kv',
                            'deployed_at' => now()->toISOString()
                        ];

                        $results['cloudflare_results'][] = [
                            'domain' => $domain,
                            'success' => true,
                            'method' => 'kv_store'
                        ];

                        Log::info("Successfully deployed to Cloudflare KV", [
                            'campaign_id' => $campaign['id'],
                            'domain' => $domain,
                            'website_id' => $website['id']
                        ]);

                        // 2. Also deploy via your existing methods (as backup/secondary)
                        try {
                            $websiteModel = Website::find($website['id']);
                            if ($websiteModel) {
                                $legacyResult = $this->deployToWebsiteLegacy($campaign, $websiteModel);

                                if ($legacyResult['success']) {
                                    Log::info("Also deployed via legacy method", [
                                        'campaign_id' => $campaign['id'],
                                        'domain' => $domain,
                                        'method' => $legacyResult['method']
                                    ]);
                                }
                            }
                        } catch (\Exception $legacyError) {
                            // Legacy deployment failed, but CF KV succeeded - this is OK
                            Log::warning("Legacy deployment failed but CF KV succeeded", [
                                'campaign_id' => $campaign['id'],
                                'domain' => $domain,
                                'legacy_error' => $legacyError->getMessage()
                            ]);
                        }
                    } else {
                        throw new \Exception("Failed to store campaign data in Cloudflare KV");
                    }
                } catch (\Exception $e) {
                    $results['success'] = false;
                    $results['failed_sites'][] = [
                        'domain' => $domain,
                        'website_id' => $website['id'],
                        'error' => $e->getMessage(),
                        'failed_at' => now()->toISOString()
                    ];

                    $results['cloudflare_results'][] = [
                        'domain' => $domain,
                        'success' => false,
                        'error' => $e->getMessage()
                    ];

                    Log::error("Failed to deploy campaign to website", [
                        'campaign_id' => $campaign['id'],
                        'domain' => $domain,
                        'website_id' => $website['id'],
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Log overall deployment result
            $this->logDeploymentResult($campaign['id'], $results);
        } catch (\Exception $e) {
            $results['success'] = false;
            $results['error'] = $e->getMessage();

            Log::error("Critical error during campaign deployment", [
                'campaign_id' => $campaign['id'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $results;
    }

    /**
     * Get websites associated with this campaign using your relationships
     */
    private function getCampaignWebsites(array $campaign): array
    {
        $campaignModel = Campaign::with('campaignWebsites.website')
            ->find($campaign['id']);

        if (!$campaignModel || $campaignModel->campaignWebsites->isEmpty()) {
            Log::warning("No websites found for campaign", [
                'campaign_id' => $campaign['id']
            ]);
            throw new \Exception("No websites found for campaign {$campaign['id']}");
        }

        $websites = $campaignModel->campaignWebsites->map(function ($campaignWebsite) {
            $website = $campaignWebsite->website;
            return [
                'id' => $website->id,
                'domain' => $this->extractDomain($website->url),
                'type' => $website->type ?? 'unknown',
                'url' => $website->url,
                'priority' => $campaignWebsite->priority ?? 1,
                'dom_selector' => $campaignWebsite->dom_selector,
                'custom_affiliate_url' => $campaignWebsite->custom_affiliate_url,
                'timer_offset' => $campaignWebsite->timer_offset,
            ];
        })->toArray();

        Log::debug("Websites loaded for deployment", [
            'campaign_id' => $campaign['id'],
            'websites' => $websites
        ]);

        return $websites;
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
     * Prepare campaign data specifically for Cloudflare KV storage
     */
    /**
     * Prepare campaign data for Cloudflare KV storage
     * 
     * @param array $campaign The campaign data
     * @param array $website The website data
     * @return array Formatted campaign data for Cloudflare KV
     * @throws \Exception If required fields are missing or invalid
     */
    /**
     * Validate and format a color value
     */
    private function validateColor(?string $color, string $default = '#000000'): string
    {
        if (empty($color)) {
            return $default;
        }

        // Check if it's a valid hex color
        if (preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $color)) {
            return $color;
        }

        // Check if it's a valid RGB/RGBA color
        if (preg_match('/^rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+(?:\s*,\s*[01]?\.?\d+\s*)?\)$/', $color)) {
            return $color;
        }

        return $default;
    }

    /**
     * Validate a URL
     */
    private function validateUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        // Add http:// if no scheme is present
        if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
            $url = 'http://' . $url;
        }

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    /**
     * Validate DOM selector
     */
    private function validateDomSelector(string $selector): string
    {
        // Basic validation - ensure it's not empty and doesn't contain dangerous characters
        $selector = trim($selector);
        if (empty($selector)) {
            return 'body'; // Default to body if empty
        }

        // Remove any potentially dangerous characters
        $selector = preg_replace('/[^a-zA-Z0-9\s\[\]=\#\.\-_~>+,"]/', '', $selector);

        return $selector ?: 'body';
    }

    /**
     * Prepare campaign data for Cloudflare KV storage
     */
    private function prepareCampaignDataForCloudflare(array $campaign, array $website): array
    {
        // Validate required fields
        $requiredFields = ['id', 'name', 'start_at'];
        foreach ($requiredFields as $field) {
            if (empty($campaign[$field])) {
                throw new \Exception("Missing required field: {$field}");
            }
        }

        // Format dates to ISO-8601 strings
        $startDate = !empty($campaign['start_at'])
            ? Carbon::parse($campaign['start_at'])->toIso8601String()
            : null;

        $endDate = !empty($campaign['end_at'])
            ? Carbon::parse($campaign['end_at'])->toIso8601String()
            : null;

        // Build the campaign data
        $campaignData = [
            'id' => (string) $campaign['id'],
            'name' => $campaign['name'],
            'title' => $campaign['name'], // Using name as display title
            'content' => $campaign['description'] ?? "Check out our latest campaign: {$campaign['name']}",
            'background_color' => $this->validateColor($campaign['background_color'] ?? '#007cba'),
            'text_color' => $this->validateColor($campaign['text_color'] ?? '#ffffff'),
            'cta_text' => $campaign['cta_text'] ?? 'Learn More',
            'cta_url' => $this->validateUrl($website['custom_affiliate_url'] ?? $campaign['custom_affiliate_url'] ?? null),
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        $campaignData['active'] = ($campaign['status'] ?? 'inactive') === 'active';
        $campaignData['priority'] = (int) ($campaign['priority'] ?? 1);
        $campaignData['dom_selector'] = $this->validateDomSelector(
            $website['dom_selector'] ?? $campaign['dom_selector'] ?? 'body'
        );
        $campaignData['triggers'] = $this->prepareTriggers($campaign['triggers'] ?? []);

        $campaignData['campaign_metadata'] = [
            'created_at' => !empty($campaign['created_at'])
                ? Carbon::parse($campaign['created_at'])->toIso8601String()
                : now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
            'deployed_at' => now()->toIso8601String(),
            'deployment_id' => uniqid('cf-', true),
            'source' => 'crm-deployment-service',
            'version' => '1.0.0',
            'original_campaign_data' => $campaign,
            'website_config' => $website,
        ];

        // Log the prepared data (without sensitive info)
        $loggableData = $campaignData;
        unset($loggableData['campaign_metadata']);

        Log::debug('Prepared campaign data for Cloudflare KV', [
            'campaign_id' => $campaign['id'],
            'domain' => $website['domain'] ?? 'unknown',
            'data_summary' => [
                'name' => $campaignData['name'],
                'active' => $campaignData['active'],
                'start_date' => $campaignData['start_date'],
                'end_date' => $campaignData['end_date'],
                'triggers_count' => count($campaignData['triggers'] ?? [])
            ]
        ]);

        return $campaignData;
    }

    /**
     * Extract clean domain from URL
     */
    private function extractDomain(string $url): string
    {
        $parsed = parse_url($url);
        return $parsed['host'] ?? $url;
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
     * Prepare triggers for the campaign
     * 
     * @param array $triggers
     * @return array
     */
    protected function prepareTriggers(array $triggers): array
    {
        $prepared = [];

        foreach ($triggers as $trigger) {
            $prepared[] = [
                'type' => $trigger['type'] ?? 'page_visit',
                'value' => $trigger['value'] ?? '1',
                'operator' => $trigger['operator'] ?? '>=',
                'enabled' => $trigger['enabled'] ?? true,
            ];
        }

        // Ensure at least one trigger exists
        if (empty($prepared)) {
            $prepared[] = [
                'type' => 'page_visit',
                'value' => '1',
                'operator' => '>=',
                'enabled' => true,
            ];
        }

        return $prepared;
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
    /**
     * Enhanced deployment result logging
     */
    protected function logDeploymentResult(int $campaignId, array $results): void
    {
        $successful = count($results['deployed_sites']);
        $failed = count($results['failed_sites']);

        CampaignDeployment::updateOrCreate(
            [
                'campaign_id' => $campaignId,
                'deployed_at' => now()->startOfMinute(),
            ],
            [
                'status' => $failed > 0 ? 'partial' : 'completed',
                'metadata' => [
                    'websites_successful' => $successful,
                    'websites_failed' => $failed,
                    'cloudflare_deployments' => count($results['cloudflare_results'] ?? []),
                    'total_sites' => $results['total_sites'] ?? 0,
                    'deployment_method' => 'cloudflare_kv_primary',
                    'results' => $results,
                ],
            ]
        );

        Log::info("Campaign deployment completed", [
            'campaign_id' => $campaignId,
            'successful_deployments' => $successful,
            'failed_deployments' => $failed,
            'total_sites' => $results['total_sites'] ?? 0
        ]);
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
    /**
     * Remove campaign from all websites (for campaign deactivation)
     */
    public function removeCampaignFromAllWebsites(array $campaign): array
    {
        $results = [
            'success' => true,
            'removed_sites' => [],
            'failed_sites' => [],
        ];

        try {
            $websites = $this->getCampaignWebsites($campaign);

            foreach ($websites as $website) {
                $domain = $website['domain'];

                if ($this->cloudflareKV->removeCampaignData($domain)) {
                    $this->cloudflareKV->invalidateCache($domain);
                    $results['removed_sites'][] = [
                        'domain' => $domain,
                        'website_id' => $website['id'],
                        'removed_at' => now()->toISOString()
                    ];

                    Log::info("Removed campaign from Cloudflare KV", [
                        'campaign_id' => $campaign['id'],
                        'domain' => $domain
                    ]);
                } else {
                    $results['success'] = false;
                    $results['failed_sites'][] = [
                        'domain' => $domain,
                        'website_id' => $website['id'],
                        'error' => 'Failed to remove from Cloudflare KV'
                    ];
                }
            }
        } catch (\Exception $e) {
            $results['success'] = false;
            Log::error("Failed to remove campaign from websites", [
                'campaign_id' => $campaign['id'],
                'error' => $e->getMessage()
            ]);
        }

        return $results;
    }

    /**
     * Your existing deployToWebsite method (renamed to avoid conflicts)
     */
    public function deployToWebsiteLegacy(array $campaign, Website $website): array
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
}
