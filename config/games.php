<?php

use RiotAPI\LeagueAPI\LeagueAPI;

return [
    'lol' => [
        
        'api_config' => [
            LeagueAPI::SET_KEY              => env('RIOT_KEY'),
            LeagueAPI::SET_TOURNAMENT_KEY   => env('RIOT_TKEY'),
            LeagueAPI::SET_REGION           => 'ru',
            LeagueAPI::SET_VERIFY_SSL       => true,           
            LeagueAPI::SET_INTERIM          => false,
            LeagueAPI::SET_CACHE_RATELIMIT  => false,
            LeagueAPI::SET_CACHE_CALLS      => false
        ],
        'provider'          => env('RIOT_PROVIDER'),
        'redirect'          => env('RIOT_REDIRECT_URI'),

        'leagues'           => [
            'iron'          => [
                'name'      => 'Железо',
                'division'  => [1 => 400, 2 => 300, 3 => 200, 4 => 100]
            ],
            'bronze'        => [
                'name'      => 'Бронза',
                'division'  => [1 => 800, 2 => 700, 3 => 600, 4 => 500]
            ],
            'silver'        => [
                'name'      => 'Серебро',
                'division'  => [1 => 1200, 2 => 1100, 3 => 1000, 4 => 900]
            ], 
            'gold'          => [
                'name'      => 'Золото',
                'division'  => [1 => 1600, 2 => 1500, 3 => 1400, 4 => 1300]
            ],
            'platinum'      => [
                'name'      => 'Платина',
                'division'  => [1 => 2000, 2 => 1900, 3 => 1800, 4 => 1700]
            ],
            'diamond'       => [
                'name'      => 'Алмаз',
                'division'  => [1 => 2400, 2 => 2300, 3 => 2200, 4 => 2100]
            ],
            'master'        => [
                'name'      => 'Мастер',
                'division'  => 2500
            ],
            'grandmaster'   => [
                'name'      => 'Гранд Мастер',
                'division'  => 2600
            ],
            'challenger'    => [
                'name'      => 'Претендент',
                'division'  => 2700
            ]
        ]
    ]
];