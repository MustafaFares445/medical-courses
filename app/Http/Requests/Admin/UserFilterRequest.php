<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

final class UserFilterRequest extends AdminFilterRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return array_merge($this->commonRules('name,-name,email,-email,createdAt,-createdAt'), [
            'filter.userType' => ['sometimes', 'nullable', 'in:admin,student,super_admin'],
            'filter.isActive' => ['sometimes', 'nullable', 'boolean'],
        ]);
    }

    public function userType(): ?string
    {
        return $this->stringFilter('userType');
    }

    public function isActive(): ?bool
    {
        return $this->booleanFilter('isActive');
    }

    public function sortColumnName(): string
    {
        return $this->sortColumn([
            'name' => 'name',
            'email' => 'email',
            'createdAt' => 'created_at',
        ], 'created_at', '-createdAt');
    }

    public function sortDirectionName(): string
    {
        return $this->sortDirection('-createdAt');
    }
}
