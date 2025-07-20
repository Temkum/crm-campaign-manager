<?php

namespace Database\Seeders;

use App\Models\Market;
use Illuminate\Database\Seeder;

class MarketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $markets = [
            ['name' => 'United Kingdom', 'iso_code' => 'GB'],
            ['name' => 'Germany', 'iso_code' => 'DE'],
            ['name' => 'Italy', 'iso_code' => 'IT'],
            ['name' => 'Spain', 'iso_code' => 'ES'],
            ['name' => 'France', 'iso_code' => 'FR'],
            ['name' => 'Portugal', 'iso_code' => 'PT'],
            ['name' => 'Netherlands', 'iso_code' => 'NL'],
            ['name' => 'Belgium', 'iso_code' => 'BE'],
            ['name' => 'Sweden', 'iso_code' => 'SE'],
            ['name' => 'Denmark', 'iso_code' => 'DK'],
            ['name' => 'Finland', 'iso_code' => 'FI'],
            ['name' => 'Greece', 'iso_code' => 'GR'],
            ['name' => 'Ireland', 'iso_code' => 'IE'],
            ['name' => 'Malta', 'iso_code' => 'MT'],
            ['name' => 'Canada', 'iso_code' => 'CA'],
            ['name' => 'United States', 'iso_code' => 'US'],
            ['name' => 'Japan', 'iso_code' => 'JP'],
            ['name' => 'Brazil', 'iso_code' => 'BR'],
            ['name' => 'India', 'iso_code' => 'IN'],
            ['name' => 'Australia', 'iso_code' => 'AU'],
            ['name' => 'South Africa', 'iso_code' => 'ZA'],
            ['name' => 'Latvia', 'iso_code' => 'LV'],
        ];

        foreach ($markets as $data) {
            Market::firstOrCreate(['iso_code' => $data['iso_code']], $data);
        }
    }
}