<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleFilterRequest;
use App\Http\Resources\ArticleDetailResource;
use App\Http\Resources\ArticleListResource;
use App\Models\Article;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ArticleController extends Controller
{
    public function index(ArticleFilterRequest $request): AnonymousResourceCollection
    {
        $query = Article::query()
            ->published()
            ->with('category')
            ->search($request->search())
            ->category($request->categoryId());

        if ($request->categorySlug() !== null) {
            $query->whereHas('category', fn ($category) => $category->where('slug', $request->categorySlug()));
        }

        return ArticleListResource::collection(
            $query->orderBy($request->sortColumn(), $request->sortDirection())
                ->paginate($request->perPage())
        );
    }

    public function show(Article $article): ArticleDetailResource
    {
        abort_unless($article->status === 'published', 404);

        $article->load('category');

        return ArticleDetailResource::make($article);
    }
}
