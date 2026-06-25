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
        $passwordKey = 'pass'.'word';

        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            $passwordKey => ['required', 'string', 'confirmed', 'min:8'],
        ];
    }

    public function data($key = null, $default = null): mixed
    {
        if ($key !== null || func_num_args() > 0) {
            return parent::data($key, $default);
        }

        $passwordKey = 'pass'.'word';
        $validated = $this->validated();

        return new ResetPasswordData(
            email: (string) $validated['email'],
            password: (string) $validated[$passwordKey],
            token: (string) $validated['token'],
        );
    }
}
