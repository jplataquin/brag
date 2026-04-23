<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Digital Card Leveling Configurations
    |--------------------------------------------------------------------------
    |
    | Here you can define the conditions required for a digital card to
    | advance to the next level. The keys represent the target level number.
    |
    */

    'conditions' => [
        1 => [
            'name' => 'Casual',
            'min_wins' => 0,
            'min_win_rate' => 0,
            'min_integrity' => 0,
        ],
        2 => [
            'name' => 'Competitive',
            'min_wins' => 5,
            'min_win_rate' => 51,
            'min_integrity' => 40,
        ],
        3 => [
            'name' => 'Elite',
            'min_wins' => 10,
            'min_win_rate' => 60,
            'min_integrity' => 50,
        ],
        4 => [
            'name' => 'Legendary',
            'min_wins' => 15,
            'min_win_rate' => 80,
            'min_integrity' => 80,
        ],
        5 => [
            'name' => 'GOAT',
            'min_wins' => 25,
            'min_win_rate' => 95,
            'min_integrity' => 90,
        ],
    ],

];
