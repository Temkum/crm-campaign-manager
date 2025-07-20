<?php

namespace Database\Seeders;

use App\Models\Website;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WebsiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $websites = [
            [
                'url' => 'https://example.com', 
                'api_url' => 'https://api.example.com', 
                'type' => 1,
                'auth_type' => 'none',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'url' => 'https://analytics-platform.net', 
                'api_url' => 'https://api.analytics-platform.net/v1', 
                'type' => 1,
                'auth_type' => 'token',
                'auth_token' => Str::random(32),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'url' => 'https://internal-dashboard.local', 
                'api_url' => null,
                'type' => 2,
                'auth_type' => 'basic',
                'auth_user' => 'admin',
                'auth_pass' => Hash::make('securePass123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'url' => 'https://legacy-system.org', 
                'api_url' => null,
                'type' => 3,
                'auth_type' => 'none',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'url' => 'https://partner-site.co', 
                'api_url' => 'https://api.partner-site.co/data', 
                'type' => 3,
                'auth_type' => 'oauth2',
                'auth_token' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'url' => 'https://dev-api.test', 
                'api_url' => 'https://dev-api.test/api/v2', 
                'type' => 1,
                'auth_type' => 'token',
                'auth_token' => Str::random(32),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'url' => 'https://intranet.company.local', 
                'api_url' => 'https://intranet.company.local/api', 
                'type' => 2,
                'auth_type' => 'basic',
                'auth_user' => 'intra_admin',
                'auth_pass' => Hash::make('intranetPass'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'url' => 'https://external-service.io', 
                'api_url' => 'https://external-service.io/rest', 
                'type' => 2,
                'auth_type' => 'api_key',
                'auth_token' => 'key_' . Str::random(24),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'url' => 'https://staging-app.dev', 
                'api_url' => 'https://staging-app.dev/api', 
                'type' => 1,
                'auth_type' => 'none',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'url' => 'https://monitoring-tool.app', 
                'api_url' => 'https://monitoring-tool.app/metrics', 
                'type' => 2,
                'auth_type' => 'basic',
                'auth_user' => 'monitor',
                'auth_pass' => Hash::make('monitor123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'url' => 'https://thirdparty.api', 
                'api_url' => 'https://thirdparty.api/v1', 
                'type' => 3,
                'auth_type' => 'token',
                'auth_token' => Str::random(35),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'url' => 'https://old-cms.legacy', 
                'api_url' => null,
                'type' => 3,
                'auth_type' => 'basic',
                'auth_user' => 'cmsadmin',
                'auth_pass' => Hash::make('cmsLegacyPass'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($websites as $data) {
            Website::firstOrCreate(['url' => $data['url']], $data);
        }
    }
}