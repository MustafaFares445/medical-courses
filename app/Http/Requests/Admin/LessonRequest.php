<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

final class LessonRequest extends FormRequest
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
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'title' => [$required, 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'summary' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'content' => ['sometimes', 'nullable', 'string'],
            'videoUrl' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
            'status' => [$required, 'string', 'in:draft,published,hidden'],
            'lessonVideo' => ['sometimes', 'nullable', 'file', 'mimetypes:video/mp4,video/webm', 'max:102400'],
        ];
    }
}
