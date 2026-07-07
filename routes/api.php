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
Route::get('/books', [API\BookController::class, 'index']);
Route::get('/books/{book:slug}', [API\BookController::class, 'show']);
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
    Route::get('/books/{book}/file', [API\Admin\TextbookController::class, 'file'])
        ->name('admin.books.file')
        ->middleware('signed');
    Route::apiResource('books', API\Admin\TextbookController::class);
    Route::apiResource('articles', API\Admin\EditorialController::class);
    Route::apiResource('admins', API\Admin\AdminUserController::class)
        ->middleware(\App\Http\Middleware\SuperAdminMiddleware::class);
    Route::patch('users/{user}/active', [API\Admin\UserController::class, 'updateActive']);
    Route::apiResource('users', API\Admin\UserController::class)->only(['index', 'store', 'show']);
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
