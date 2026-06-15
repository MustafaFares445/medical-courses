<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class CourseRequest extends FormRequest
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

        $currency = $this->input('currency');
        if (is_string($currency)) {
            $this->merge(['currency' => strtoupper($currency)]);
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var Course|null $course */
        $course = $this->route('course');
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'categoryId' => [
                'sometimes',
                'nullable',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('type', 'course')),
            ],
            'title' => [$required, 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('courses', 'slug')->ignore($course?->id)],
            'shortDescription' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => [$required, 'numeric', 'min:0'],
            'currency' => [$required, 'string', 'size:3'],
            'status' => [$required, 'string', 'in:draft,published,hidden'],
            'thumbnail' => ['sometimes', 'nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
