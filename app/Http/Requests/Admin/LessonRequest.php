<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Support\Locale;
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
        $source = Locale::slugSource($title);

        if ($source !== '' && (! is_string($slug) || $slug === '')) {
            $this->merge(['slug' => Str::slug($source)]);
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'title' => [$required, 'array'],
            'title.en' => [$required, 'string', 'max:255'],
            'title.ar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'summary' => ['sometimes', 'nullable', 'array'],
            'summary.en' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'summary.ar' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'content' => ['sometimes', 'nullable', 'array'],
            'content.en' => ['sometimes', 'nullable', 'string'],
            'content.ar' => ['sometimes', 'nullable', 'string'],
            'videoUrl' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
            'status' => [$required, 'string', 'in:draft,published,hidden'],
            'lessonVideo' => ['sometimes', 'nullable', 'file', 'mimetypes:video/mp4,video/webm', 'max:102400'],
        ];
    }
}
