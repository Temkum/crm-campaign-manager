<?php

namespace App\Jobs;

use App\Services\CampaignDeploymentExecutorService;
use App\Services\DeploymentValidator;
use App\Models\Website;
use App\Models\CampaignDeployment;
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

        // Create or update CampaignDeployment record
        $deployment = CampaignDeployment::firstOrCreate([
            'campaign_id' => $this->campaign['id'],
        ], [
            'status' => 'in_progress',
            'deployed_at' => now(),
            'metadata' => [],
        ]);

        $deployment->status = 'in_progress';
        $deployment->deployed_at = now();
        $deployment->save();

        Log::info("Deploying campaign {$this->campaign['id']} to website {$website->url}");

        try {
            $result = $deploymentExecutor->deployToWebsite($this->campaign, $website);

            if ($result['success']) {
                // Run async verification
                $validator = app(DeploymentValidator::class);
                $verification = $validator->validate($this->campaign, $website);

                $deployment->status = $verification['success'] ? 'successful' : 'failed';
                $deployment->metadata = array_merge($deployment->metadata ?? [], [
                    'deployment_result' => $result,
                    'verification' => $verification,
                    'context' => [
                        'campaign_id' => $this->campaign['id'],
                        'website_id' => $this->websiteId,
                        'website_url' => $website->url,
                        'executed_at' => now()->toDateTimeString(),
                    ],
                ]);
                $deployment->deployed_at = now();
                $deployment->save();

                Log::info("Successfully deployed and verified", [
                    'deployment_id' => $deployment->id,
                    'verification' => $verification,
                ]);
            } else {
                $deployment->status = 'failed';
                $deployment->metadata = array_merge($deployment->metadata ?? [], [
                    'deployment_result' => $result,
                    'context' => [
                        'campaign_id' => $this->campaign['id'],
                        'website_id' => $this->websiteId,
                        'website_url' => $website->url ?? null,
                        'executed_at' => now()->toDateTimeString(),
                    ],
                ]);
                $deployment->save();
                Log::warning("Failed to deploy to website", $result);
                throw new \Exception($result['error'] ?? 'Unknown deployment error');
            }
        } catch (\Exception $e) {
            $deployment->status = 'failed';
            $deployment->metadata = array_merge($deployment->metadata ?? [], [
                'error' => $e->getMessage(),
                'context' => [
                    'campaign_id' => $this->campaign['id'],
                    'website_id' => $this->websiteId,
                    'website_url' => isset($website) ? $website->url : null,
                    'executed_at' => now()->toDateTimeString(),
                ],
            ]);
            $deployment->save();
            Log::error("Website deployment failed", [
                'website_id' => $this->websiteId,
                'website_url' => isset($website) ? $website->url : null,
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
