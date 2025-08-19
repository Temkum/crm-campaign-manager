<?php

test('it can create a campaign trigger', function () {
    $trigger = \App\Models\CampaignTrigger::factory()->make();
    expect($trigger)->toBeInstanceOf(\App\Models\CampaignTrigger::class);
});
