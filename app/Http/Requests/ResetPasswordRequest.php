<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\Auth\ResetPasswordData;
use Illuminate\Foundation\Http\FormRequest;

final class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ];
    }

    public function data(): ResetPasswordData
    {
        $validated = $this->validated();

        return new ResetPasswordData(
            email: (string) $validated['email'],
            password: (string) $validated['password'],
            token: (string) $validated['token'],
        );
    }
}
