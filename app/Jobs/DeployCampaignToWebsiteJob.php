<?php

namespace App\Jobs;

use App\Services\CampaignDeploymentExecutorService;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeployCampaignToWebsiteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $campaign;
    public int $websiteId;
    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(array $campaign, int $websiteId)
    {
        $this->campaign = $campaign;
        $this->websiteId = $websiteId;
        $this->onQueue('campaign-deployment');
    }

    public function handle(CampaignDeploymentExecutorService $deploymentExecutor): void
    {
        $website = Website::find($this->websiteId);

        if (!$website) {
            Log::error("Website not found for deployment", [
                'website_id' => $this->websiteId,
                'campaign_id' => $this->campaign['id'],
            ]);
            return;
        }

        Log::info("Deploying campaign {$this->campaign['id']} to website {$website->url}");

        try {
            $result = $deploymentExecutor->deployToWebsite($this->campaign, $website);

            if ($result['success']) {
                Log::info("Successfully deployed to website", $result);
            } else {
                Log::warning("Failed to deploy to website", $result);
                throw new \Exception($result['error'] ?? 'Unknown deployment error');
            }
        } catch (\Exception $e) {
            Log::error("Website deployment failed", [
                'website_id' => $this->websiteId,
                'website_url' => $website->url,
                'campaign_id' => $this->campaign['id'],
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Website deployment job failed permanently", [
            'campaign_id' => $this->campaign['id'],
            'website_id' => $this->websiteId,
            'error' => $exception->getMessage(),
        ]);
    }
}
