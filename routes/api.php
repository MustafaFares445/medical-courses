<?php

declare(strict_types=1);

use App\Http\Controllers\API\AccountRecoveryController;
use App\Http\Controllers\API\Admin;
use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookAccessController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CheckoutController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\CurrentUserController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\LibraryController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProtectedLessonController;
use App\Http\Controllers\API\StripeWebhookController;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => ApiResponse::success(['status' => 'ok', 'service' => 'medical-courses-api']));

Route::get('/home', HomeController::class);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{course:slug}', [CourseController::class, 'show']);
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{book:slug}', [BookController::class, 'show']);
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{article:slug}', [ArticleController::class, 'show']);
Route::post('/stripe/webhook', StripeWebhookController::class);

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
    Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/me', [CurrentUserController::class, 'show']);
    Route::patch('/me', [CurrentUserController::class, 'update']);
    Route::prefix('my')->group(function (): void {
        Route::get('/health', fn () => ApiResponse::success(['status' => 'authenticated']));
        Route::get('/library', LibraryController::class);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order:order_number}', [OrderController::class, 'show']);
        Route::get('/courses/{course}/lessons/{lesson}', [ProtectedLessonController::class, 'show']);
        Route::get('/books/{book}/access', [BookAccessController::class, 'show']);
    });
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function (): void {
    Route::get('/health', fn () => ApiResponse::success(['status' => 'admin']));
    Route::get('/overview', Admin\OverviewController::class);

    Route::apiResource('categories', Admin\CategoryController::class);
    Route::apiResource('courses', Admin\CourseController::class);

    Route::get('courses/{course}/sections', [Admin\CourseSectionController::class, 'index']);
    Route::post('courses/{course}/sections', [Admin\CourseSectionController::class, 'store']);
    Route::get('course-sections/{section}', [Admin\CourseSectionController::class, 'show']);
    Route::patch('course-sections/{section}', [Admin\CourseSectionController::class, 'update']);
    Route::delete('course-sections/{section}', [Admin\CourseSectionController::class, 'destroy']);

    Route::get('course-sections/{section}/lessons', [Admin\LessonController::class, 'index']);
    Route::post('course-sections/{section}/lessons', [Admin\LessonController::class, 'store']);
    Route::get('lessons/{lesson}', [Admin\LessonController::class, 'show']);
    Route::patch('lessons/{lesson}', [Admin\LessonController::class, 'update']);
    Route::delete('lessons/{lesson}', [Admin\LessonController::class, 'destroy']);
});
