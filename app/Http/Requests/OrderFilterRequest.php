<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class OrderFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perPage' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'filter' => ['sometimes', 'array'],
            'filter.status' => ['sometimes', 'nullable', 'in:pending,paid,failed,cancelled,expired'],
        ];
    }

    public function perPage(int $default = 10): int
    {
        return (int) $this->integer('perPage', $default);
    }

    public function status(): ?string
    {
        $value = $this->input('filter.status');

        return is_string($value) && $value !== '' ? $value : null;
    }
}
