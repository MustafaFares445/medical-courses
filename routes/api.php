<?php

declare(strict_types=1);

use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => ApiResponse::success([
    'status' => 'ok',
    'service' => 'medical-courses-api',
]));

Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/me', fn (Request $request) => ApiResponse::success([
        'user' => $request->user(),
    ]));

    Route::prefix('my')->group(function (): void {
        Route::get('/health', fn () => ApiResponse::success([
            'status' => 'authenticated',
        ]));
    });
});

Route::middleware(['auth:sanctum', 'admin'])
    ->prefix('admin')
    ->group(function (): void {
        Route::get('/health', fn () => ApiResponse::success([
            'status' => 'admin',
        ]));
    });
