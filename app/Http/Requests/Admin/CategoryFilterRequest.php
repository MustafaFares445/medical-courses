<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

final class CategoryFilterRequest extends AdminFilterRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return array_merge($this->commonRules('name,-name,createdAt,-createdAt'), [
            'filter.type' => ['sometimes', 'nullable', 'in:course,book,article'],
            'filter.isActive' => ['sometimes', 'nullable', 'boolean'],
        ]);
    }

    public function type(): ?string
    {
        return $this->stringFilter('type');
    }

    public function isActive(): ?bool
    {
        return $this->booleanFilter('isActive');
    }

    public function sortColumnName(): string
    {
        return $this->sortColumn([
            'name' => 'name',
            'createdAt' => 'created_at',
        ], 'name', 'name');
    }

    public function sortDirectionName(): string
    {
        return $this->sortDirection('name');
    }
}
