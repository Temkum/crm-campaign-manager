<?php

namespace App\Console\Commands;

use App\Services\CloudflareKVService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestCloudflareKV extends Command
{
    protected $signature = 'cf:test-kv {domain} {--remove}';
    protected $description = 'Test Cloudflare KV integration';

    public function handle(CloudflareKVService $cfService)
    {
        $domain = $this->argument('domain');

        if ($this->option('remove')) {
            $this->info("Removing test data for domain: {$domain}");
            $result = $cfService->removeCampaignData($domain);
            $this->info($result ? "✅ Successfully removed data" : "❌ Failed to remove data");
            return;
        }

        $testData = [
            'campaigns' => [
                [
                    'id' => 14,
                    'name' => 'PMU Mobile France - Sports Betting',
                    'description' => 'William Hill - Italy',
                    'status' => 'active',
                    'priority' => 10,
                    'start_at' => now()->format('Y-m-d\TH:i:s.000000\Z'),
                    'end_at' => now()->addMonths(6)->format('Y-m-d\TH:i:s.000000\Z'),
                    'affiliate_config' => [
                        'url' => 'https://affiliate-William Hill.com?campaign=14&website=13',
                        'display_type' => 'iframe',
                        'width' => '90%',
                        'height' => '90%',
                        'max_width' => '800px',
                        'max_height' => '600px',
                        'position' => 'center',
                        'backdrop' => true,
                        'backdrop_color' => 'rgba(0, 0, 0, 0.8)',
                        'close_button' => true,
                        'close_on_escape' => true,
                        'close_on_backdrop_click' => false
                    ],
                    'triggers' => [
                        'logic' => 'AND',
                        'groups' => [
                            [
                                'logic' => 'AND',
                                'name' => 'Mobile Users',
                                'conditions' => [
                                    [
                                        'type' => 'device',
                                        'operator' => 'equals',
                                        'value' => 'Mobile',
                                        'description' => 'Mobile devices only'
                                    ],
                                    [
                                        'type' => 'url',
                                        'operator' => 'contains',
                                        'value' => 'sport',
                                        'description' => 'Sports pages only'
                                    ],
                                    [
                                        'type' => 'hour',
                                        'operator' => 'between',
                                        'value' => ['8', '23'],
                                        'description' => 'Business hours 8h-23h'
                                    ],
                                    [
                                        'type' => 'country',
                                        'operator' => 'in',
                                        'value' => ['FR', 'BE', 'CH'],
                                        'description' => 'Allowed countries: FR, BE, CH'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'frequency' => [
                        'type' => 'once_per_session',
                        'cooldown_hours' => 24,
                        'max_displays_per_user' => 3
                    ],
                    'scheduling' => [
                        'days_of_week' => [1, 2, 3, 4, 5, 6, 7],
                        'hours_range' => [
                            'start' => 8,
                            'end' => 23
                        ],
                        'timezone' => 'Europe/Paris'
                    ],
                    'delay' => [
                        'type' => 'fixed',
                        'value' => 5,
                        'unit' => 'seconds'
                    ]
                ]
            ],
            'global_settings' => [
                'max_concurrent_campaigns' => 1,
                'fallback_country' => 'FR',
                'debug_mode' => false,
                'tracking' => [
                    'enabled' => true,
                    'events' => [
                        'triggered',
                        'displayed',
                        'closed',
                        'clicked',
                        'converted'
                    ],
                    'store_user_data' => true
                ],
                'performance' => [
                    'lazy_load' => true,
                    'cache_duration' => 300,
                    'max_retries' => 3,
                    'timeout' => 5000
                ]
            ]
        ];

        $this->info("Storing test data for domain: {$domain}");
        $result = $cfService->storeCampaignData($domain, $testData);

        if ($result) {
            $this->info("✅ Successfully stored test data");
            $this->info("You can now visit https://{$domain} to view the raw data");

            // Test cache invalidation
            $this->info("Testing cache invalidation...");
            $cacheResult = $cfService->invalidateCache($domain);
            $this->info($cacheResult ? "✅ Cache invalidated successfully" : "❌ Cache invalidation failed");
        } else {
            $this->error("❌ Failed to store test data. Check your logs for more details.");
        }
    }
}
