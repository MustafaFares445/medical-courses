<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Data\Admin\PostData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleFilterRequest;
use App\Http\Requests\Admin\ArticleRequest;
use App\Http\Resources\Admin\ArticleAdminResource;
use App\Models\Article;
use App\Services\Admin\PostService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class EditorialController extends Controller
{
    public function index(ArticleFilterRequest $request): AnonymousResourceCollection
    {
        $query = Article::query()->with('category')->search($request->search());
        if ($request->status() !== null) { $query->where('status', $request->status()); }
        if ($request->categoryId() !== null) { $query->where('category_id', $request->categoryId()); }
        if ($request->createdAfter() !== null) { $query->whereDate('created_at', '>=', $request->createdAfter()); }
        if ($request->createdBefore() !== null) { $query->whereDate('created_at', '<=', $request->createdBefore()); }
        return ArticleAdminResource::collection($query->orderBy($request->sortColumnName(), $request->sortDirectionName())->paginate($request->perPage()));
    }

    public function store(ArticleRequest $request, PostService $service): JsonResponse
    {
        $article = $service->create($this->data($request->validated()), $request->file('articleImage'));
        return ArticleAdminResource::make($article->load('category'))->response()->setStatusCode(201);
    }

    public function show(Article $article): ArticleAdminResource
    {
        return ArticleAdminResource::make($article->load('category'));
    }

    public function update(ArticleRequest $request, Article $article, PostService $service): ArticleAdminResource
    {
        $article = $service->update($article, $this->data($request->validated()), $request->file('articleImage'));
        return ArticleAdminResource::make($article->load('category'));
    }

    public function destroy(Article $article, PostService $service): JsonResponse
    {
        $service->delete($article);
        return ApiResponse::noContent();
    }

    private function data(array $validated): PostData
    {
        return new PostData(
            categoryId: array_key_exists('categoryId', $validated) && $validated['categoryId'] !== null ? (int) $validated['categoryId'] : null,
            title: is_array($validated['title'] ?? null) ? $validated['title'] : null,
            slug: is_string($validated['slug'] ?? null) ? $validated['slug'] : null,
            excerpt: array_key_exists('excerpt', $validated) && is_array($validated['excerpt']) ? $validated['excerpt'] : null,
            body: array_key_exists('body', $validated) && is_array($validated['body']) ? $validated['body'] : null,
            status: is_string($validated['status'] ?? null) ? $validated['status'] : null,
            fields: array_keys($validated),
        );
    }
}
