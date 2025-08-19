<?php

test('it can instantiate CloudflareKVService', function () {
    $service = app(\App\Services\CloudflareKVService::class);
    expect($service)->toBeInstanceOf(\App\Services\CloudflareKVService::class);
});
