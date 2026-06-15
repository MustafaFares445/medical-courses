<?php

declare(strict_types=1);

use App\Http\Controllers\API\AccountRecoveryController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CurrentUserController;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => ApiResponse::success([
    'status' => 'ok',
    'service' => 'medical-courses-api',
]));

Route::prefix('auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/forgot-password', [AccountRecoveryController::class, 'forgot'])->middleware('throttle:5,1');
    Route::post('/reset-password', [AccountRecoveryController::class, 'reset'])->middleware('throttle:5,1');

    Route::middleware(['auth:sanctum'])->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/me', [CurrentUserController::class, 'show']);
    Route::patch('/me', [CurrentUserController::class, 'update']);

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
