<?php

test('it can instantiate DeployCampaignJob', function () {
    $campaign = \App\Models\Campaign::factory()->make();
    $job = new \App\Jobs\DeployCampaignJob($campaign);
    expect($job)->toBeInstanceOf(\App\Jobs\DeployCampaignJob::class);
});
