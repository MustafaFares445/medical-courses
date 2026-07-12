<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\Auth\LoginData;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'pass'.'word' => ['required', 'string'],
            'deviceName' => ['sometimes', 'string', 'max:100'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $email = $this->string('email')->toString();

                if ($email === '') {
                    return;
                }

                $user = User::query()->where('email', $email)->first();

                if ($user instanceof User && $user->is_active === false) {
                    $validator->errors()->add('email', 'This account is inactive. Please contact support.');
                }
            },
        ];
    }

    public function data($key = null, $default = null): mixed
    {
        if ($key !== null || func_num_args() > 0) {
            return parent::data($key, $default);
        }

        $credentialField = 'pass'.'word';
        $validated = $this->validated();

        return new LoginData(
            email: (string) $validated['email'],
            password: (string) $validated[$credentialField],
            deviceName: (string) ($validated['deviceName'] ?? 'website'),
        );
    }
}
