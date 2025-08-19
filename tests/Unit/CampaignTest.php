<?php

test('it can create a campaign', function () {
    $campaign = \App\Models\Campaign::factory()->make();
    expect($campaign)->toBeInstanceOf(\App\Models\Campaign::class);
});
