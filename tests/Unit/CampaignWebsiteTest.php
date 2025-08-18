<?php

test('it can create a campaign website', function () {
    $website = \App\Models\CampaignWebsite::factory()->make();
    expect($website)->toBeInstanceOf(\App\Models\CampaignWebsite::class);
});
