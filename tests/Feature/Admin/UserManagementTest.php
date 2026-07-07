<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('allows admins to create student users', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $this->postJson('/api/admin/users', [
        'name' => 'New Student',
        'email' => 'student@example.com',
        'password' => 'secret12345',
        'isActive' => true,
    ])->assertCreated()
        ->assertJsonPath('data.name', 'New Student')
        ->assertJsonPath('data.email', 'student@example.com')
        ->assertJsonPath('data.userType', User::TYPE_STUDENT)
        ->assertJsonPath('data.isActive', true);

    $this->assertDatabaseHas('users', [
        'email' => 'student@example.com',
        'user_type' => User::TYPE_STUDENT,
        'is_active' => true,
    ]);
});

it('validates admin student user creation payloads', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $this->postJson('/api/admin/users', [
        'name' => '',
        'email' => 'not-an-email',
        'password' => 'short',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});
