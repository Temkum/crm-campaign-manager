<?php

test('it can instantiate the CampaignDeploymentExecutorService', function () {
    $service = app(\App\Services\CampaignDeploymentExecutorService::class);
    expect($service)->toBeInstanceOf(\App\Services\CampaignDeploymentExecutorService::class);
});
