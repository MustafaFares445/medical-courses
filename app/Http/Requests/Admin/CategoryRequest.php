<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Category;
use App\Support\Locale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $name = $this->input('name');
        $slug = $this->input('slug');
        $source = Locale::slugSource($name);

        if ($source !== '' && (! is_string($slug) || $slug === '')) {
            $this->merge(['slug' => Str::slug($source)]);
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var Category|null $category */
        $category = $this->route('category');
        $required = $this->isMethod('post') ? 'required' : 'sometimes';
        $type = (string) $this->input('type', $category?->type);

        return [
            'type' => [$required, 'string', 'in:course,book,article'],
            'name' => [$required, 'array'],
            'name.en' => [$required, 'string', 'max:255'],
            'name.ar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')
                    ->where(fn ($query) => $query->where('type', $type))
                    ->ignore($category?->id),
            ],
            'description' => ['sometimes', 'nullable', 'array'],
            'description.en' => ['sometimes', 'nullable', 'string'],
            'description.ar' => ['sometimes', 'nullable', 'string'],
            'isActive' => ['sometimes', 'boolean'],
        ];
    }
}
