<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\Auth\ProfileData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()?->id),
            ],
        ];
    }

    public function data($key = null, $default = null): mixed
    {
        if ($key !== null || func_num_args() > 0) {
            return parent::data($key, $default);
        }

        $validated = $this->validated();

        return new ProfileData(
            name: (string) $validated['name'],
            email: (string) $validated['email'],
        );
    }
}
