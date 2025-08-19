<?php

test('it can create a campaign deployment', function () {
    $deployment = \App\Models\CampaignDeployment::factory()->make();
    expect($deployment)->toBeInstanceOf(\App\Models\CampaignDeployment::class);
});
