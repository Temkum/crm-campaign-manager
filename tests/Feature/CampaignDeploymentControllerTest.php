<?php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('campaign deployment endpoint returns 200', function () {
    $response = $this->getJson('/api/campaign-deployment');
    $response->assertStatus(200);
});
