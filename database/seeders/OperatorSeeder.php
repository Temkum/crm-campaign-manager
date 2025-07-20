<?php

namespace Database\Seeders;

use App\Models\Operator;
use Illuminate\Database\Seeder;

class OperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $operators = [
            [
                'name' => 'Bet365',
                'website_url' => 'https://www.bet365.com',
                'logo_url' => 'https://www.bet365.com/assets/logo.png',
            ],
            [
                'name' => 'Flutter Entertainment',
                'website_url' => 'https://www.flutterentertainment.com',
                'logo_url' => 'https://www.flutterentertainment.com/logo.png',
            ],
            [
                'name' => 'Paddy Power',
                'website_url' => 'https://www.paddypower.com',
                'logo_url' => 'https://www.paddypower.com/images/logo.png',
            ],
            [
                'name' => 'Betway',
                'website_url' => 'https://www.betway.com',
                'logo_url' => 'https://www.betway.com/brand/betway-logo.svg',
            ],
            [
                'name' => '888 Holdings',
                'website_url' => 'https://www.888.com',
                'logo_url' => 'https://www.888.com/media/images/logo.png',
            ],
            [
                'name' => 'Ladbrokes',
                'website_url' => 'https://www.ladbrokes.com',
                'logo_url' => 'https://www.ladbrokes.com/images/ladbrokes-logo.png',
            ],
            [
                'name' => 'Coral',
                'website_url' => 'https://www.coral.co.uk',
                'logo_url' => 'https://www.coral.co.uk/static/images/coral-logo.png',
            ],
            [
                'name' => 'Entain',
                'website_url' => 'https://www.entainplc.com',
                'logo_url' => 'https://www.entainplc.com/wp-content/uploads/2021/09/Entain-Logo.png',
            ],
            [
                'name' => 'Kindred Group',
                'website_url' => 'https://www.kindredgroup.com',
                'logo_url' => 'https://www.kindredgroup.com/globalassets/logos/kindred-logo.png',
            ],
            [
                'name' => 'Unibet',
                'website_url' => 'https://www.unibet.com',
                'logo_url' => 'https://www.unibet.com/resources/images/unibet-logo.svg',
            ],
            [
                'name' => 'William Hill',
                'website_url' => 'https://www.williamhill.com',
                'logo_url' => 'https://www.williamhill.com/images/wh-logo.svg',
            ],
            [
                'name' => 'DraftKings',
                'website_url' => 'https://www.draftkings.com',
                'logo_url' => 'https://www.draftkings.com/_next/static/chunks/dkpwo-logo-horizontal-white.svg',
            ],
            [
                'name' => 'FanDuel',
                'website_url' => 'https://www.fanduel.com',
                'logo_url' => 'https://www.fanduel.com/static/fd-logo.svg',
            ],
            [
                'name' => 'PokerStars',
                'website_url' => 'https://www.pokerstars.com',
                'logo_url' => 'https://www.pokerstars.com/files/pstars-logo.png',
            ],
            [
                'name' => 'Betfred',
                'website_url' => 'https://www.betfred.com',
                'logo_url' => 'https://www.betfred.com/images/betfred-logo.png',
            ],
            [
                'name' => 'bwin',
                'website_url' => 'https://www.bwin.com',
                'logo_url' => 'https://www.bwin.com/static/bwin-logo.png',
            ],
            [
                'name' => 'Casumo',
                'website_url' => 'https://www.casumo.com',
                'logo_url' => 'https://www.casumo.com/images/casumo-logo.png',
            ],
            [
                'name' => 'LeoVegas',
                'website_url' => 'https://www.leovegas.com',
                'logo_url' => 'https://www.leovegas.com/images/leovegas-logo.png',
            ],
            [
                'name' => 'MrGreen',
                'website_url' => 'https://www.mrgreen.com',
                'logo_url' => 'https://www.mrgreen.com/images/mrgreen-logo.png',
            ],
            [
                'name' => 'Betsson',
                'website_url' => 'https://www.betsson.com',
                'logo_url' => 'https://www.betsson.com/images/betsson-logo.png',
            ],
        ];

        foreach ($operators as $operator) {
            Operator::firstOrCreate(['name' => $operator['name']], $operator);
        }
    }
}