<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class HomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'coursesLimit' => ['sometimes', 'integer', 'min:1', 'max:12'],
            'booksLimit' => ['sometimes', 'integer', 'min:1', 'max:12'],
            'articlesLimit' => ['sometimes', 'integer', 'min:1', 'max:12'],
        ];
    }

    public function coursesLimit(): int
    {
        return (int) $this->integer('coursesLimit', 6);
    }

    public function booksLimit(): int
    {
        return (int) $this->integer('booksLimit', 6);
    }

    public function articlesLimit(): int
    {
        return (int) $this->integer('articlesLimit', 3);
    }
}
