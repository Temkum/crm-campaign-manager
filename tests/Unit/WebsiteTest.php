<?php

test('it can create a website', function () {
    $website = \App\Models\Website::factory()->make();
    expect($website)->toBeInstanceOf(\App\Models\Website::class);
});