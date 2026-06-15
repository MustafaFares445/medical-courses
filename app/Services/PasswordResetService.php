<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Auth\ResetPasswordData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class PasswordResetService
{
    public function sendResetLink(string $email): string
    {
        return Password::sendResetLink(['email' => $email]);
    }

    /** @throws ValidationException */
    public function reset(ResetPasswordData $data): string
    {
        $status = Password::reset(
            [
                'email' => $data->email,
                'password' => $data->password,
                'password_confirmation' => $data->password,
                'token' => $data->token,
            ],
            function (User $user) use ($data): void {
                $user->forceFill([
                    'password' => Hash::make($data->password),
                    'remember_token' => Str::random(60),
                ])->save();
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return $status;
    }
}
