<?php

return [
    'defaults' => [
        'currency' => env('DEFAULT_CURRENCY', 'EUR'),
        'locale'   => env('DEFAULT_LOCALE', 'de_DE'),
    ],
    'currencies' => [
        'EUR',
        'USD',
        'GBP',
    ],
];