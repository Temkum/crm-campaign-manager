<?php
use Illuminate\Support\Facades\Queue;
use App\Jobs\DeployCampaignJob;
use App\Models\Campaign;

test('deploy campaign job is dispatched', function () {
    Queue::fake();
    $campaign = Campaign::factory()->create();
    DeployCampaignJob::dispatch($campaign);
    Queue::assertPushed(DeployCampaignJob::class);
});