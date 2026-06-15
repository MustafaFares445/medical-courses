<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

it('sends a password reset notification', function (): void {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'student@example.com',
    ]);

    $this->postJson('/api/auth/forgot-password', [
        'email' => 'student@example.com',
    ])->assertOk()
        ->assertJsonStructure(['data' => ['message']]);

    Notification::assertSentTo($user, ResetPassword::class);
});

it('resets a user password with a valid token', function (): void {
    $user = User::factory()->create([
        'email' => 'student@example.com',
        'password' => 'old-password',
    ]);

    $token = Password::broker()->createToken($user);

    $this->postJson('/api/auth/reset-password', [
        'email' => 'student@example.com',
        'token' => $token,
        'password' => 'new-password123',
        'password_confirmation' => 'new-password123',
    ])->assertOk()
        ->assertJsonStructure(['data' => ['message']]);

    expect(Hash::check('new-password123', $user->refresh()->password))->toBeTrue();
});
