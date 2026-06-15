<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryFilterRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class CategoryController extends Controller
{
    public function index(CategoryFilterRequest $request): AnonymousResourceCollection
    {
        $query = Category::query()
            ->where('is_active', true)
            ->search($request->search());

        if ($request->type() !== null) {
            $query->where('type', $request->type());
        }

        return CategoryResource::collection(
            $query->orderBy($request->sortColumn(), $request->sortDirection())
                ->paginate($request->perPage())
        );
    }
}
