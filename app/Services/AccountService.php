<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Auth\LoginData;
use App\Data\Auth\ProfileData;
use App\Data\Auth\RegisterData;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class AccountService
{
    /** @return array{user: User, accessToken: string} */
    public function register(RegisterData $data): array
    {
        $user = User::query()->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
            'user_type' => 'student',
        ]);

        return [
            'user' => $user,
            'accessToken' => $user->createToken('website')->plainTextToken,
        ];
    }

    /** @return array{user: User, accessToken: string} */
    public function login(LoginData $data): array
    {
        $user = User::query()->where('email', $data->email)->first();

        if (! $user instanceof User || ! Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        Auth::login($user);

        return [
            'user' => $user,
            'accessToken' => $user->createToken($data->deviceName)->plainTextToken,
        ];
    }

    public function logout(User $user): void
    {
        $currentAccessToken = $user->currentAccessToken();

        if ($currentAccessToken !== null && method_exists($currentAccessToken, 'delete')) {
            $currentAccessToken->delete();
        }

        Auth::guard('web')->logout();
    }

    public function updateProfile(User $user, ProfileData $data): User
    {
        $user->forceFill([
            'name' => $data->name,
            'email' => $data->email,
        ])->save();

        return $user->refresh();
    }
}
