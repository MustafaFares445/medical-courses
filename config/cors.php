<?php

declare(strict_types=1);

$originValues = [
    env('FRONTEND_URL'),
    env('DASHBOARD_URL'),
    ...explode(',', (string) env('CORS_ALLOWED_ORIGINS', 'https://iass-mocha.vercel.app,https://iass-dashboard.vercel.app,http://10.5.0.2:3000,http://localhost:3000,http://127.0.0.1:3000,http://localhost:5173,http://127.0.0.1:5173')),
];

$allowedOrigins = array_values(array_unique(array_filter(
    array_map(static fn ($origin): string => rtrim(trim((string) $origin), '/'), $originValues)
)));

$originPatterns = array_values(array_filter(
    array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGIN_PATTERNS', '/^https:\/\/.*\.vercel\.app$/')))
));

return [
    'paths' => ['api/*', 'storage/*', 'up'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_origins' => $allowedOrigins,
    'allowed_origins_patterns' => $originPatterns,
    'allowed_headers' => ['Accept', 'Authorization', 'Content-Type', 'X-Requested-With', 'X-Accept-Language', 'Accept-Language'],
    'exposed_headers' => ['Accept-Ranges', 'Content-Length', 'Content-Range', 'Content-Type'],
    'max_age' => 86400,
    'supports_credentials' => false,
];
