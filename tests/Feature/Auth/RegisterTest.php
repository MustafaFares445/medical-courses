<?php

declare(strict_types=1);

use App\Models\User;

it('registers a new student user', function (): void {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Student User',
        'email' => 'student@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'userType' => 'admin',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.user.email', 'student@example.com')
        ->assertJsonPath('data.user.userType', 'student')
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'name', 'email', 'userType'],
                'token',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'student@example.com',
        'user_type' => 'student',
    ]);
});

it('rejects duplicate registration email', function (): void {
    User::factory()->create(['email' => 'student@example.com']);

    $this->postJson('/api/auth/register', [
        'name' => 'Student User',
        'email' => 'student@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});
