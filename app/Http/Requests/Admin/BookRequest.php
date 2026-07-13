<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Book;
use App\Support\Locale;
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
        $source = Locale::slugSource($this->input('title'));
        $slug = $this->input('slug');
        if ($source !== '' && (! is_string($slug) || $slug === '')) {
            $this->merge(['slug' => Str::slug($source)]);
        }
    }

    public function rules(): array
    {
        $book = $this->route('book');
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'categoryId' => ['sometimes', 'nullable', Rule::exists('categories', 'id')->where(fn ($query) => $query->where('type', 'book'))],
            'title' => [$required, 'array'], 'title.en' => [$required, 'string', 'max:255'], 'title.ar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('books', 'slug')->ignore($book instanceof Book ? $book->id : null)],
            'shortDescription' => ['sometimes', 'nullable', 'array'], 'shortDescription.en' => ['sometimes', 'nullable', 'string', 'max:1000'], 'shortDescription.ar' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'description' => ['sometimes', 'nullable', 'array'], 'description.en' => ['sometimes', 'nullable', 'string'], 'description.ar' => ['sometimes', 'nullable', 'string'],
            'price' => [$required, 'numeric', 'min:0'], 'currency' => [$required, 'string', 'size:3'],
            'status' => [$required, 'string', 'in:draft,published,hidden'], 'cover' => ['sometimes', 'nullable', 'file', 'max:4096'], 'bookFile' => [$required, 'file', 'max:51200'],
        ];
    }
}
