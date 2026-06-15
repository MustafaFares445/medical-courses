<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class BookRequest extends FormRequest
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
        /** @var Book|null $book */
        $book = $this->route('book');
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'categoryId' => ['sometimes', 'nullable', Rule::exists('categories', 'id')->where(fn ($query) => $query->where('type', 'book'))],
            'title' => [$required, 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('books', 'slug')->ignore($book?->id)],
            'shortDescription' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => [$required, 'numeric', 'min:0'],
            'currency' => [$required, 'string', 'size:3'],
            'externalFileUrl' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'status' => [$required, 'string', 'in:draft,published,hidden'],
            'cover' => ['sometimes', 'nullable', 'file', 'max:4096'],
            'bookFile' => ['sometimes', 'nullable', 'file', 'max:51200'],
        ];
    }
}
