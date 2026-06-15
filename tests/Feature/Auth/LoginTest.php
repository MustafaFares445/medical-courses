<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('logs in a user with valid credentials', function (): void {
    User::factory()->create([
        'email' => 'student@example.com',
        'password' => 'password123',
    ]);

    $this->postJson('/api/auth/login', [
        'email' => 'student@example.com',
        'password' => 'password123',
    ])->assertOk()
        ->assertJsonPath('data.user.email', 'student@example.com')
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'name', 'email', 'userType'],
                'token',
            ],
        ]);
});

it('rejects invalid login credentials', function (): void {
    User::factory()->create([
        'email' => 'student@example.com',
        'password' => 'password123',
    ]);

    $this->postJson('/api/auth/login', [
        'email' => 'student@example.com',
        'password' => 'wrong-password',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('logs out an authenticated user', function (): void {
    Sanctum::actingAs(User::factory()->student()->create());

    $this->postJson('/api/auth/logout')
        ->assertOk()
        ->assertJsonPath('data.message', 'Logged out successfully.');
});
