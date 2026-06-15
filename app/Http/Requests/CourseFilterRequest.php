<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CourseFilterRequest extends FormRequest
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
            'filter.categoryId' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'filter.category' => ['sometimes', 'nullable', 'string', 'max:255'],
            'filter.categorySlug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'filter.priceMin' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'filter.priceMax' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'sort' => ['sometimes', 'in:title,-title,price,-price,publishedAt,-publishedAt,createdAt,-createdAt'],
        ];
    }

    public function perPage(int $default = 12): int
    {
        return (int) $this->integer('perPage', $default);
    }

    public function search(): ?string
    {
        $value = $this->input('search');

        return is_string($value) && $value !== '' ? $value : null;
    }

    public function categoryId(): ?int
    {
        $value = $this->input('filter.categoryId');

        return $value === null || $value === '' ? null : (int) $value;
    }

    public function categorySlug(): ?string
    {
        $value = $this->input('filter.categorySlug', $this->input('filter.category'));

        return is_string($value) && $value !== '' ? $value : null;
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

    public function sortColumn(): string
    {
        return match (ltrim((string) $this->input('sort', '-publishedAt'), '-')) {
            'title' => 'title',
            'price' => 'price',
            'createdAt' => 'created_at',
            default => 'published_at',
        };
    }

    public function sortDirection(): string
    {
        return str_starts_with((string) $this->input('sort', '-publishedAt'), '-') ? 'desc' : 'asc';
    }
}
