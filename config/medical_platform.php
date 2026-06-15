<?php

declare(strict_types=1);

return [
    'pagination' => [
        'default_per_page' => (int) env('API_DEFAULT_PER_PAGE', 15),
        'max_per_page' => (int) env('API_MAX_PER_PAGE', 100),
    ],

    'admin_user_type' => 'admin',

    'default_currency' => env('APP_DEFAULT_CURRENCY', 'usd'),
];
