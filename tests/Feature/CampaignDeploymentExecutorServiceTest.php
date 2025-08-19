<?php
use App\Services\CampaignDeploymentExecutorService;
use App\Services\CampaignDeploymentService;
use App\Services\CloudflareKVService;
use App\Models\Campaign;

test('manual deployment returns expected structure', function () {
    $service = new CampaignDeploymentExecutorService(
        app(CampaignDeploymentService::class),
        app(CloudflareKVService::class)
    );
    $result = $service->deployManually([1], false);
    expect($result)->toBeArray();
    expect($result)->toHaveKey('success');
});
