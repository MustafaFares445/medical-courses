<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class CourseSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'title' => [$required, 'array'],
            'title.en' => [$required, 'string', 'max:255'],
            'title.ar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'array'],
            'description.en' => ['sometimes', 'nullable', 'string'],
            'description.ar' => ['sometimes', 'nullable', 'string'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
