<?php

declare(strict_types=1);

it('allows configured website and dashboard origins', function (string $origin): void {
    $this->get('/api/health', [
        'Origin' => $origin,
    ])->assertOk()
        ->assertHeader('Access-Control-Allow-Origin', $origin);
})->with([
    'website vercel' => 'https://iass-mocha.vercel.app',
    'dashboard vercel' => 'https://iass-dashboard.vercel.app',
    'website local' => 'http://10.5.0.2:3000',
    'dashboard local' => 'http://localhost:5173',
    'website local default port' => 'http://localhost:3000',
]);
