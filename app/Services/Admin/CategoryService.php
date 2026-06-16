<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Data\Admin\CategoryData;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

final class CategoryService
{
    public function create(CategoryData $data): Category
    {
        return DB::transaction(fn (): Category => Category::query()->create($data->toModelAttributes()));
    }

    public function update(Category $category, CategoryData $data): Category
    {
        return DB::transaction(function () use ($category, $data): Category {
            $category->forceFill($data->toModelAttributes($category))->save();

            return $category->refresh();
        });
    }

    public function delete(Category $category): void
    {
        if ($category->courses()->exists() || $category->books()->exists() || $category->articles()->exists()) {
            throw new ConflictHttpException('Category cannot be deleted while it is assigned to content.');
        }

        DB::transaction(fn (): ?bool => $category->delete());
    }
}
