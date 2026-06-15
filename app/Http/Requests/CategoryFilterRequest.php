<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CategoryFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perPage' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'filter' => ['sometimes', 'array'],
            'filter.type' => ['sometimes', 'nullable', 'in:course,book,article'],
            'sort' => ['sometimes', 'in:name,-name,createdAt,-createdAt'],
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

    public function type(): ?string
    {
        $value = $this->input('filter.type');

        return is_string($value) && $value !== '' ? $value : null;
    }

    public function sortColumn(): string
    {
        return str_contains((string) $this->input('sort', 'name'), 'createdAt') ? 'created_at' : 'name';
    }

    public function sortDirection(): string
    {
        return str_starts_with((string) $this->input('sort', 'name'), '-') ? 'desc' : 'asc';
    }
}
