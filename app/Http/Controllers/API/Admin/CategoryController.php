<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Data\Admin\CategoryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryFilterRequest;
use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Resources\Admin\CategoryAdminResource;
use App\Models\Category;
use App\Services\Admin\CategoryService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class CategoryController extends Controller
{
    public function index(CategoryFilterRequest $request): AnonymousResourceCollection
    {
        $query = Category::query()
            ->withCount(['courses', 'books', 'articles'])
            ->search($request->search());

        if ($request->type() !== null) {
            $query->where('type', $request->type());
        }

        if ($request->isActive() !== null) {
            $query->where('is_active', $request->isActive());
        }

        if ($request->createdAfter() !== null) {
            $query->whereDate('created_at', '>=', $request->createdAfter());
        }

        if ($request->createdBefore() !== null) {
            $query->whereDate('created_at', '<=', $request->createdBefore());
        }

        return CategoryAdminResource::collection(
            $query->orderBy($request->sortColumnName(), $request->sortDirectionName())
                ->paginate($request->perPage())
        );
    }

    public function store(CategoryRequest $request, CategoryService $service): JsonResponse
    {
        $category = $service->create(CategoryData::fromValidated($request->validated()));

        return CategoryAdminResource::make($category->loadCount(['courses', 'books', 'articles']))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Category $category): CategoryAdminResource
    {
        return CategoryAdminResource::make($category->loadCount(['courses', 'books', 'articles']));
    }

    public function update(CategoryRequest $request, Category $category, CategoryService $service): CategoryAdminResource
    {
        $category = $service->update($category, CategoryData::fromValidated($request->validated()));

        return CategoryAdminResource::make($category->loadCount(['courses', 'books', 'articles']));
    }

    public function destroy(Category $category, CategoryService $service): JsonResponse
    {
        $service->delete($category);

        return ApiResponse::noContent();
    }
}
