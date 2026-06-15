<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $title = $this->input('title');
        $slug = $this->input('slug');

        if (is_string($title) && $title !== '' && (! is_string($slug) || $slug === '')) {
            $this->merge(['slug' => Str::slug($title)]);
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var Article|null $article */
        $article = $this->route('article');
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'categoryId' => [
                'sometimes',
                'nullable',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('type', 'article')),
            ],
            'title' => [$required, 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('articles', 'slug')->ignore($article?->id)],
            'excerpt' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'body' => ['required_if:status,published', 'sometimes', 'nullable', 'string'],
            'status' => [$required, 'string', 'in:draft,published,hidden'],
            'articleImage' => ['sometimes', 'nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
