<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

final class ArticleFilterRequest extends AdminFilterRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return array_merge($this->commonRules('title,-title,publishedAt,-publishedAt,createdAt,-createdAt'), [
            'filter.status' => ['sometimes', 'nullable', 'in:draft,published,hidden'],
            'filter.categoryId' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
        ]);
    }

    public function status(): ?string
    {
        return $this->stringFilter('status');
    }

    public function categoryId(): ?int
    {
        return $this->integerFilter('categoryId');
    }

    public function sortColumnName(): string
    {
        return $this->sortColumn([
            'title' => 'title',
            'publishedAt' => 'published_at',
            'createdAt' => 'created_at',
        ], 'created_at', '-createdAt');
    }

    public function sortDirectionName(): string
    {
        return $this->sortDirection('-createdAt');
    }
}
