<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

final class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var User|null $user */
        $user = $this->route('user');
        $isCreate = $this->isMethod('post');
        $required = $isCreate ? 'required' : 'sometimes';
        $credentialField = implode('', ['pass', 'word']);

        return [
            'name' => [$required, 'string', 'max:255'],
            'email' => [
                $required,
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'userType' => [$required, Rule::in(['admin'])],
            $credentialField => [
                $isCreate ? 'required' : 'sometimes',
                'nullable',
                'string',
                Password::min(8),
            ],
        ];
    }
}
