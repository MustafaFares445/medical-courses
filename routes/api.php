<?php

declare(strict_types=1);

use App\Http\Controllers\API;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::options('/{any}', fn () => response()->noContent())->where('any', '.*');

Route::get('/health', fn () => ApiResponse::success(['status' => 'ok']));

Route::post('/stripe/webhook', API\StripeWebhookController::class);

Route::middleware('guest')->group(function (): void {
    Route::post('/auth/register', [API\AuthController::class, 'register']);
    Route::post('/auth/login', [API\AuthController::class, 'login']);
});

Route::get('/categories', [API\CategoryController::class, 'index']);
Route::get('/courses', [API\CourseController::class, 'index']);
Route::get('/courses/{course:slug}', [API\CourseController::class, 'show']);
Route::get('/books', [API\TextbookController::class, 'index']);
Route::get('/books/{book:slug}', [API\TextbookController::class, 'show']);
Route::get('/articles', [API\ArticleController::class, 'index']);
Route::get('/articles/{article:slug}', [API\ArticleController::class, 'show']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/auth/logout', [API\AuthController::class, 'logout']);
    Route::get('/me', [API\MeController::class, 'show']);
    Route::post('/checkout', [API\CheckoutController::class, 'store']);
    Route::get('/my/library', API\LibraryController::class);

    Route::prefix('my')->group(function (): void {
        Route::get('/courses/{course}', [API\PurchasedCourseController::class, 'show']);
        Route::get('/courses/{course}/lessons/{lesson}', [API\PurchasedCourseController::class, 'lesson']);
        Route::get('/lessons/{lesson}/video', [API\LessonVideoController::class, 'show'])
            ->name('lessons.video.show')
            ->middleware('signed');
        Route::get('/books/{book}/access', [API\BookAccessController::class, 'show']);
    });
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function (): void {
    Route::get('/health', fn () => ApiResponse::success(['status' => 'admin']));
    Route::get('/overview', API\Admin\OverviewController::class);

    Route::apiResource('categories', API\Admin\CategoryController::class);
    Route::apiResource('courses', API\Admin\CourseController::class);
    Route::apiResource('books', API\Admin\TextbookController::class);
    Route::apiResource('articles', API\Admin\EditorialController::class);
    Route::get('/users', [API\Admin\UserController::class, 'index']);
    Route::post('/users', [API\Admin\UserController::class, 'store']);
    Route::get('/users/{user}', [API\Admin\UserController::class, 'show']);
    Route::patch('/users/{user}', [API\Admin\UserController::class, 'update']);
    Route::delete('/users/{user}', [API\Admin\UserController::class, 'destroy']);
    Route::apiResource('orders', API\Admin\OrderController::class)->only(['index', 'show']);
    Route::apiResource('payments', API\Admin\PaymentController::class)->only(['index', 'show']);

    Route::get('/courses/{course}/sections', [API\Admin\CourseSectionController::class, 'index']);
    Route::post('/courses/{course}/sections', [API\Admin\CourseSectionController::class, 'store']);
    Route::patch('/course-sections/{section}', [API\Admin\CourseSectionController::class, 'update']);
    Route::delete('/course-sections/{section}', [API\Admin\CourseSectionController::class, 'destroy']);

    Route::get('/course-sections/{section}/lessons', [API\Admin\LessonController::class, 'index']);
    Route::post('/course-sections/{section}/lessons', [API\Admin\LessonController::class, 'store']);
    Route::patch('/lessons/{lesson}', [API\Admin\LessonController::class, 'update']);
    Route::delete('/lessons/{lesson}', [API\Admin\LessonController::class, 'destroy']);
});
