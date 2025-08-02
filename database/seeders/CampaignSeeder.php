<?php

namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory;
use Illuminate\Support\Facades\DB;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $campaigns = [];
        $statuses = ['active', 'disabled', 'paused', 'scheduled', 'completed'];

        // get existing market and operator
        $operatorIds = DB::table('operators')->pluck('id');
        $marketIds = DB::table('markets')->pluck('id');

        if ($operatorIds->isEmpty() || $marketIds->isEmpty()) {
            $this->command->error("Operators or Markets table is empty. Please seed them first.");
            return;
        }

        for ($i = 1; $i <= 15; $i++) {
            $campaigns[] = [
                'name' => 'Campaign ' . $i,
                'operator_id' => $operatorIds->random(),
                'market_id' => $marketIds->random(),
                'start_at' => $faker->dateTimeBetween('-1 week', '+1 week'),
                'end_at' => $faker->dateTimeBetween('+1 week', '+2 weeks'),
                'status' => $faker->randomElement($statuses),
                'priority' => $faker->numberBetween(1, 10),
                'duration' => $faker->numberBetween(1, 10),
                'rotation_delay' => $faker->numberBetween(1, 10),
                'dom_selector' => $faker->randomElement(['body', 'head', 'title']),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Get all website IDs
        $websiteIds = DB::table('websites')->pluck('id');
        if ($websiteIds->isEmpty()) {
            $this->command->error("Websites table is empty. Please seed it first.");
            return;
        }

        foreach ($campaigns as $campaign) {
            $campaignModel = Campaign::firstOrCreate(['name' => $campaign['name']], $campaign);

            // Attach at least 1 website (random)
            $numWebsites = $faker->numberBetween(1, min(3, count($websiteIds)));
            $chosenWebsiteIds = $websiteIds->random($numWebsites)->all();
            foreach ($chosenWebsiteIds as $websiteId) {
                \App\Models\CampaignWebsite::firstOrCreate([
                    'campaign_id' => $campaignModel->id,
                    'website_id' => $websiteId,
                ], [
                    'priority' => $faker->numberBetween(1, 10),
                    'dom_selector' => $faker->randomElement(['body', 'head', 'title']),
                    'custom_affiliate_url' => $faker->optional()->url,
                    'timer_offset' => $faker->optional()->numberBetween(0, 300) ?? 0,
                ]);
            }

            // Attach at least 1 trigger (random)
            $triggerTypes = ['time', 'click', 'scroll', 'hover'];
            $triggerOperators = ['=', '>', '<', '>=', '<='];
            $numTriggers = $faker->numberBetween(1, 2);
            for ($t = 0; $t < $numTriggers; $t++) {
                \App\Models\CampaignTrigger::firstOrCreate([
                    'campaign_id' => $campaignModel->id,
                    'type' => $faker->randomElement($triggerTypes),
                    'operator' => $faker->randomElement($triggerOperators),
                    'value' => $faker->word,
                ]);
            }
        }
    }
}
