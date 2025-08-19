<?php

test('it can create a campaign trigger group', function () {
    $group = \App\Models\CampaignTriggerGroup::factory()->make();
    expect($group)->toBeInstanceOf(\App\Models\CampaignTriggerGroup::class);
});
