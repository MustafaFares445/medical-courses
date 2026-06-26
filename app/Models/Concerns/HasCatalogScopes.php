<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait HasCatalogScopes
{
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if ($status === null || $status === '') {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeCategory(Builder $query, int|string|null $categoryId): Builder
    {
        if ($categoryId === null || $categoryId === '') {
            return $query;
        }

        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search === null || trim($search) === '') {
            return $query;
        }

        $term = '%'.trim($search).'%';
        $columns = property_exists($this, 'searchable') ? $this->searchable : ['title', 'name', 'slug'];
        $translatable = property_exists($this, 'translatable') ? $this->translatable : [];

        return $query->where(function (Builder $subQuery) use ($columns, $term, $translatable): void {
            foreach ($columns as $column) {
                if (in_array($column, $translatable, true)) {
                    $wrapped = DB::getQueryGrammar()->wrap($column);
                    $subQuery
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT({$wrapped}, '$.en')) LIKE ?", [$term])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT({$wrapped}, '$.ar')) LIKE ?", [$term]);

                    continue;
                }

                $subQuery->orWhere($column, 'like', $term);
            }
        });
    }

    public function scopeCreatedAfter(Builder $query, ?string $date): Builder
    {
        return $date ? $query->whereDate('created_at', '>=', $date) : $query;
    }

    public function scopeCreatedBefore(Builder $query, ?string $date): Builder
    {
        return $date ? $query->whereDate('created_at', '<=', $date) : $query;
    }
}
