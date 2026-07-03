<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

final class AdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var User|null $admin */
        $admin = $this->route('admin');
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'name' => [$required, 'string', 'max:255'],
            'email' => [
                $required,
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($admin?->id),
            ],
            'password' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'nullable',
                'string',
                Password::min(8),
            ],
        ];
    }
}
