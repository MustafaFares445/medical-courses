<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\Auth\LoginData;
use Illuminate\Foundation\Http\FormRequest;

final class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'deviceName' => ['sometimes', 'string', 'max:100'],
        ];
    }

    public function data(): LoginData
    {
        $validated = $this->validated();

        return new LoginData(
            email: (string) $validated['email'],
            password: (string) $validated['password'],
            deviceName: (string) ($validated['deviceName'] ?? 'website'),
        );
    }
}
