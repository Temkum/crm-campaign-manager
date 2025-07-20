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
        $statuses = ['active', 'inactive', 'pending', 'paused'];

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

        foreach ($campaigns as $campaign) {
            Campaign::firstOrCreate(['name' => $campaign['name']], $campaign);
        }
    }
}