<?php

namespace App\Services;

use App\Models\Website;
use Illuminate\Support\Facades\Http;

class DeploymentValidator
{
    /**
     * Validate if campaign payload is deployed to the website.
     *
     * @param array $campaign
     * @param Website $website
     * @return array [success => bool, message => string, data => mixed]
     */
    public function validate(array $campaign, Website $website): array
    {
        switch ($website->type) {
            case 'static':
                return $this->validateStatic($campaign, $website);
            case 'wordpress':
                return $this->validateWordPress($campaign, $website);
            case 'laravel':
                return $this->validateLaravel($campaign, $website);
            default:
                return $this->validateOther($campaign, $website);
        }
    }

    protected function validateStatic(array $campaign, Website $website): array
    {
        // Example: Check for a keyword or file presence
        $url = rtrim($website->url, '/') . '/index.html';
        $response = Http::timeout(15)->get($url);
        if ($response->successful() && str_contains($response->body(), $campaign['name'])) {
            return [
                'success' => true,
                'message' => 'Campaign keyword found in static HTML',
                'data' => null,
            ];
        }
        return [
            'success' => false,
            'message' => 'Campaign keyword not found in static HTML',
            'data' => $response->body(),
        ];
    }

    protected function validateWordPress(array $campaign, Website $website): array
    {
        $endpoint = rtrim($website->url, '/') . '/wp-json/wp/v2/pages';
        $response = Http::timeout(15)->get($endpoint);
        if ($response->successful() && str_contains($response->body(), $campaign['name'])) {
            return [
                'success' => true,
                'message' => 'Campaign found via WordPress REST API',
                'data' => $response->json(),
            ];
        }
        return [
            'success' => false,
            'message' => 'Campaign not found via WordPress REST API',
            'data' => $response->body(),
        ];
    }

    protected function validateLaravel(array $campaign, Website $website): array
    {
        $endpoint = rtrim($website->url, '/') . '/api/campaigns/' . $campaign['id'];
        $response = Http::timeout(15)->get($endpoint);
        if ($response->successful() && str_contains($response->body(), $campaign['name'])) {
            return [
                'success' => true,
                'message' => 'Campaign found via Laravel API',
                'data' => $response->json(),
            ];
        }
        return [
            'success' => false,
            'message' => 'Campaign not found via Laravel API',
            'data' => $response->body(),
        ];
    }

    protected function validateOther(array $campaign, Website $website): array
    {
        // Configurable endpoint or HTTP ping
        $endpoint = $website->api_url ?? $website->url;
        $response = Http::timeout(15)->get($endpoint);
        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'Received HTTP 200 from custom endpoint',
                'data' => $response->body(),
            ];
        }
        return [
            'success' => false,
            'message' => 'No valid response from custom endpoint',
            'data' => $response->body(),
        ];
    }
}
