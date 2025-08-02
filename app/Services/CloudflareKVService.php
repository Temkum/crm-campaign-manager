<?php

// 1. First, create a Cloudflare KV service
// app/Services/CloudflareKVService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class CloudflareKVService
{
    private string $apiToken;
    private string $accountId;
    private string $namespaceId;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiToken = config('services.cloudflare.api_token');
        $this->accountId = config('services.cloudflare.account_id');
        $this->namespaceId = config('services.cloudflare.kv_namespace_id');
        $this->baseUrl = "https://api.cloudflare.com/client/v4/accounts/{$this->accountId}/storage/kv/namespaces/{$this->namespaceId}";

        if (!$this->apiToken || !$this->accountId || !$this->namespaceId) {
            throw new Exception('Cloudflare configuration missing. Check your .env file.');
        }
    }

    /**
     * Store campaign data for a specific domain
     */
    public function storeCampaignData(string $domain, array $campaignData): bool
    {
        Log::info("storeCampaignData called", ['domain' => $domain, 'campaignData' => $campaignData]);
        try {
            Log::info("About to PUT to Cloudflare KV", ['url' => "{$this->baseUrl}/values/{$domain}", 'data' => $campaignData]);
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->put(
                "{$this->baseUrl}/values/{$domain}",
                $campaignData
            );

            if ($response->successful()) {
                Log::info("Successfully stored campaign data in CF KV", [
                    'domain' => $domain,
                    'campaign_id' => $campaignData['id'] ?? 'unknown'
                ]);
                return true;
            }

            Log::error("Failed to store campaign data in CF KV", [
                'domain' => $domain,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return false;
        } catch (Exception $e) {
            Log::error("Exception storing campaign data in CF KV", [
                'domain' => $domain,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Remove campaign data for a specific domain
     */
    public function removeCampaignData(string $domain): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->delete("{$this->baseUrl}/values/{$domain}");

            if ($response->successful()) {
                Log::info("Successfully removed campaign data from CF KV", ['domain' => $domain]);
                return true;
            }

            Log::error("Failed to remove campaign data from CF KV", [
                'domain' => $domain,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return false;
        } catch (Exception $e) {
            Log::error("Exception removing campaign data from CF KV", [
                'domain' => $domain,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Invalidate Cloudflare cache for specific URLs
     */
    public function invalidateCache(string $domain, array $urls = []): bool
    {
        try {
            // Default URLs to invalidate
            $defaultUrls = [
                "https://{$domain}/__campaign",
                "https://{$domain}/campaign",
            ];

            $urlsToInvalidate = array_merge($defaultUrls, $urls);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post("https://api.cloudflare.com/client/v4/zones/{$this->getZoneId($domain)}/purge_cache", [
                'files' => $urlsToInvalidate
            ]);

            if ($response->successful()) {
                Log::info("Successfully invalidated CF cache", [
                    'domain' => $domain,
                    'urls' => $urlsToInvalidate
                ]);
                return true;
            }

            Log::warning("Failed to invalidate CF cache", [
                'domain' => $domain,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return false;
        } catch (Exception $e) {
            Log::error("Exception invalidating CF cache", [
                'domain' => $domain,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get zone ID for a domain (simplified - you might want to cache this)
     */
    private function getZoneId(string $domain): ?string
    {
        // You might want to store zone IDs in your database or cache
        // For now, this is a simple lookup
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->get("https://api.cloudflare.com/client/v4/zones", [
                'name' => $domain
            ]);

            if ($response->successful()) {
                $zones = $response->json('result');
                return $zones[0]['id'] ?? null;
            }
        } catch (Exception $e) {
            Log::error("Failed to get zone ID", ['domain' => $domain, 'error' => $e->getMessage()]);
        }

        return null;
    }
}
