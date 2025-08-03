<?php

namespace App\Console\Commands;

use App\Services\CampaignDeploymentExecutorService;
use Illuminate\Console\Command;

class DeployCampaignsCommand extends Command
{
    protected $signature = 'campaigns:deploy 
                           {--force : Force deployment regardless of schedule}
                           {--campaign=* : Specific campaign IDs to deploy}
                           {--sync : Deploy synchronously instead of using queue}';

    protected $description = 'Deploy campaigns to their associated websites';

    public function handle(CampaignDeploymentExecutorService $deploymentExecutor)
    {
        $this->info('Starting campaign deployment...');

        try {
            if ($this->option('campaign')) {
                // Deploy specific campaigns
                $campaignIds = array_map('intval', $this->option('campaign'));
                $this->info("Deploying specific campaigns: " . implode(', ', $campaignIds));

                $result = $deploymentExecutor->deployManually(
                    $campaignIds,
                    !$this->option('sync')
                );
            } else {
                // Deploy all ready campaigns
                $this->info('Deploying all ready campaigns...');
                $result = $deploymentExecutor->deployAutomatically();
            }

            if ($result['success']) {
                $this->info($result['message']);
                if (isset($result['deployed_count'])) {
                    $this->info("Deployed campaigns: {$result['deployed_count']}");
                }
            } else {
                $this->error($result['message']);
                if (isset($result['validation'])) {
                    $this->displayValidationErrors($result['validation']);
                }
            }
        } catch (\Exception $e) {
            $this->error("Deployment failed: {$e->getMessage()}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function displayValidationErrors(array $validation): void
    {
        if (!empty($validation['invalid_campaigns'])) {
            $this->error('Invalid campaigns found:');
            foreach ($validation['invalid_campaigns'] as $campaignId => $details) {
                $this->line("  Campaign {$campaignId} ({$details['campaign_name']}):");
                foreach ($details['issues'] as $issue) {
                    $this->line("    - {$issue}");
                }
            }
        }
    }
}
