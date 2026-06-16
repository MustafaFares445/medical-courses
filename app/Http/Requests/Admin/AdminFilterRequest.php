<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

abstract class AdminFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    protected function commonRules(string $sortAllowList): array
    {
        return [
            'perPage' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'filter' => ['sometimes', 'array'],
            'filter.createdAfter' => ['sometimes', 'nullable', 'date'],
            'filter.createdBefore' => ['sometimes', 'nullable', 'date'],
            'sort' => ['sometimes', 'in:'.$sortAllowList],
        ];
    }

    public function perPage(int $default = 20): int
    {
        return (int) $this->integer('perPage', $default);
    }

    public function search(): ?string
    {
        $value = $this->input('search');

        return is_string($value) && $value !== '' ? $value : null;
    }

    public function createdAfter(): ?string
    {
        return $this->stringFilter('createdAfter');
    }

    public function createdBefore(): ?string
    {
        return $this->stringFilter('createdBefore');
    }

    /**
     * @param  array<string, string>  $map
     */
    public function sortColumn(array $map, string $defaultColumn = 'created_at', string $defaultSort = '-createdAt'): string
    {
        $sort = ltrim((string) $this->input('sort', $defaultSort), '-');

        return $map[$sort] ?? $defaultColumn;
    }

    public function sortDirection(string $defaultSort = '-createdAt'): string
    {
        return str_starts_with((string) $this->input('sort', $defaultSort), '-') ? 'desc' : 'asc';
    }

    protected function stringFilter(string $key): ?string
    {
        $value = $this->input('filter.'.$key);

        return is_string($value) && $value !== '' ? $value : null;
    }

    protected function integerFilter(string $key): ?int
    {
        $value = $this->input('filter.'.$key);

        return $value === null || $value === '' ? null : (int) $value;
    }

    protected function booleanFilter(string $key): ?bool
    {
        $value = $this->input('filter.'.$key);

        if ($value === null || $value === '') {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
    }
}
