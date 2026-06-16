<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

final class CourseSectionFilterRequest extends AdminFilterRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return $this->commonRules('sortOrder,-sortOrder,createdAt,-createdAt,title,-title');
    }

    public function sortColumnName(): string
    {
        return $this->sortColumn([
            'sortOrder' => 'sort_order',
            'createdAt' => 'created_at',
            'title' => 'title',
        ], 'sort_order', 'sortOrder');
    }

    public function sortDirectionName(): string
    {
        return $this->sortDirection('sortOrder');
    }
}
