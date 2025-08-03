<?php

namespace App\Jobs;

use App\Services\CampaignDeploymentExecutorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeployCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $campaign;
    public int $tries = 3;
    public int $backoff = 60; // 1 minute backoff between retries

    public function __construct(array $campaign)
    {
        $this->campaign = $campaign;
        $this->onQueue('campaign-deployment');
    }

    public function handle(CampaignDeploymentExecutorService $deploymentExecutor): void
    {
        Log::info("Starting deployment for campaign {$this->campaign['id']}", [
            'campaign_id' => $this->campaign['id'],
            'campaign_name' => $this->campaign['name'],
        ]);

        try {
            $result = $deploymentExecutor->deployToAllWebsites($this->campaign);

            if ($result['success']) {
                Log::info("Successfully deployed campaign {$this->campaign['id']}", $result);
            } else {
                Log::warning("Partial deployment failure for campaign {$this->campaign['id']}", $result);
            }
        } catch (\Exception $e) {
            Log::error("Failed to deploy campaign {$this->campaign['id']}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Campaign deployment job failed permanently", [
            'campaign_id' => $this->campaign['id'],
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Optionally notify administrators
        // Mail::to('admin@example.com')->send(new DeploymentFailedMail($this->campaign, $exception));
    }
}
