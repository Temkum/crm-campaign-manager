<?php

test('it can create a market', function () {
    $market = \App\Models\Market::factory()->make();
    expect($market)->toBeInstanceOf(\App\Models\Market::class);
});
