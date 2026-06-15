<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeRequest;
use App\Http\Resources\ArticleListResource;
use App\Http\Resources\BookListResource;
use App\Http\Resources\CourseListResource;
use App\Services\HomeService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

final class HomeController extends Controller
{
    public function __construct(private readonly HomeService $home) {}

    public function __invoke(HomeRequest $request): JsonResponse
    {
        $summaries = $this->home->summaries(
            coursesLimit: $request->coursesLimit(),
            booksLimit: $request->booksLimit(),
            articlesLimit: $request->articlesLimit(),
        );

        return ApiResponse::success([
            'latestCourses' => CourseListResource::collection($summaries['latestCourses']),
            'latestBooks' => BookListResource::collection($summaries['latestBooks']),
            'latestArticles' => ArticleListResource::collection($summaries['latestArticles']),
        ]);
    }
}
