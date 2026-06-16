<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

final class BookFilterRequest extends AdminFilterRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return array_merge($this->commonRules('title,-title,price,-price,publishedAt,-publishedAt,createdAt,-createdAt'), [
            'filter.status' => ['sometimes', 'nullable', 'in:draft,published,hidden'],
            'filter.categoryId' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'filter.priceMin' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'filter.priceMax' => ['sometimes', 'nullable', 'numeric', 'min:0'],
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

    public function priceMin(): ?float
    {
        $value = $this->input('filter.priceMin');

        return $value === null || $value === '' ? null : (float) $value;
    }

    public function priceMax(): ?float
    {
        $value = $this->input('filter.priceMax');

        return $value === null || $value === '' ? null : (float) $value;
    }

    public function sortColumnName(): string
    {
        return $this->sortColumn([
            'title' => 'title',
            'price' => 'price',
            'publishedAt' => 'published_at',
            'createdAt' => 'created_at',
        ], 'created_at', '-createdAt');
    }

    public function sortDirectionName(): string
    {
        return $this->sortDirection('-createdAt');
    }
}
