<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\Website;
use App\Models\CampaignWebsite;
use App\Models\CampaignTrigger;
use App\Services\CampaignDeploymentExecutorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TestDeploymentFlow extends Command
{
    protected $signature = 'deployment:test {--cleanup : Remove test data after test} {--campaign-id= : Use existing campaign ID}';
    protected $description = 'Test the end-to-end deployment flow with Cloudflare KV';

    public function handle(CampaignDeploymentExecutorService $deploymentService)
    {
        $this->info("🚀 Starting deployment flow test...");

        $campaign = $this->option('campaign-id')
            ? $this->useExistingCampaign($this->option('campaign-id'))
            : $this->createTestCampaign();

        if (!$campaign) {
            $this->error("❌ Failed to prepare test campaign");
            return 1;
        }

        $this->info("📋 Testing deployment for campaign ID: {$campaign->id}");

        // Deploy the campaign
        $this->info("🔄 Deploying campaign...");
        $result = $deploymentService->deployToAllWebsites($campaign->toArray());

        if ($result['success']) {
            $this->info("✅ Deployment successful!");
            $this->info("   Deployed to " . count($result['deployed_sites'] ?? []) . " websites");

            if (!empty($result['cloudflare_results'])) {
                $this->info("\n🔍 Cloudflare KV Results:");
                foreach ($result['cloudflare_results'] as $cfResult) {
                    $status = $cfResult['success'] ? '✅' : '❌';
                    $this->line("   {$status} {$cfResult['domain']}");
                }
            }

            // Test retrieval (optional)
            $this->info("\n🔍 Verifying KV storage...");
            $this->verifyKVStorage($campaign);
        } else {
            $this->error("❌ Deployment failed");
            $this->error(json_encode($result['error'] ?? 'Unknown error', JSON_PRETTY_PRINT));
        }

        // Cleanup if requested
        if ($this->option('cleanup') && !$this->option('campaign-id')) {
            $this->info("\n🧹 Cleaning up test data...");
            $campaign->campaignWebsites()->delete();
            $campaign->campaignTriggers()->delete();
            $campaign->delete();
            $this->info("✅ Cleanup complete");
        }

        return 0;
    }

    protected function createTestCampaign()
    {
        $this->info("Creating test campaign...");

        // Create or get a website
        $website = Website::firstOrCreate(
            ['url' => 'test-' . time() . '.example.com'],
            [
                'name' => 'Test Website',
                'type' => 1 // WORDPRESS enum value
            ]
        );

        // Get or create a test operator
        $operator = \App\Models\Operator::first();
        if (!$operator) {
            $operator = \App\Models\Operator::create([
                'name' => 'Test Operator',
                'status' => 'active'
            ]);
        }

        // Get or create a test market
        $market = \App\Models\Market::first();
        if (!$market) {
            $market = \App\Models\Market::create([
                'name' => 'Test Market',
                'code' => 'test',
                'status' => 'active'
            ]);
        }

        // Create test campaign
        $campaign = Campaign::create([
            'name' => 'Test Campaign ' . time(),
            'description' => 'Automated test campaign',
            'status' => 'active',
            'operator_id' => $operator->id,
            'market_id' => $market->id,
            'priority' => 1,
            'start_at' => now(),
            'end_at' => now()->addDays(7),
            'dom_selector' => 'body', // Default selector that works on all pages
            'background_color' => '#007bff',
            'text_color' => '#ffffff',
            'cta_text' => 'Click Me',
            'cta_url' => 'https://example.com/test',
        ]);

        // Attach website to campaign
        CampaignWebsite::create([
            'campaign_id' => $campaign->id,
            'website_id' => $website->id,
            'priority' => 1,
            'dom_selector' => 'body',
            'custom_affiliate_url' => 'https://affiliate.example.com?campaign=' . $campaign->id,
            'timer_offset' => 5,
        ]);

        // Add a test trigger
        CampaignTrigger::create([
            'campaign_id' => $campaign->id,
            'type' => 'page_visit',
            'value' => '3',
            'operator' => '>='
        ]);

        return $campaign->load('campaignWebsites.website', 'campaignTriggers');
    }

    protected function useExistingCampaign($campaignId)
    {
        $this->info("Using existing campaign ID: {$campaignId}");
        return Campaign::with('campaignWebsites.website', 'campaignTriggers')->find($campaignId);
    }

    protected function verifyKVStorage($campaign)
    {
        // This is a basic verification - you might want to enhance it
        // based on your actual KV structure and requirements
        $this->info("Verification would check KV storage here...");
        // In a real implementation, you would:
        // 1. Fetch from KV using the domain
        // 2. Verify the campaign data matches what was sent
        // 3. Check timestamps and other metadata
    }
}
