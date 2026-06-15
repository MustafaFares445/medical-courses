<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns the current authenticated user', function (): void {
    $user = User::factory()->student()->create([
        'email' => 'student@example.com',
    ]);

    Sanctum::actingAs($user);

    $this->getJson('/api/me')
        ->assertOk()
        ->assertJsonPath('data.user.email', 'student@example.com')
        ->assertJsonPath('data.user.userType', 'student');
});

it('denies guests from the current user endpoint', function (): void {
    $this->getJson('/api/me')->assertUnauthorized();
});

it('updates the current user profile', function (): void {
    $user = User::factory()->student()->create([
        'email' => 'student@example.com',
    ]);

    Sanctum::actingAs($user);

    $this->patchJson('/api/me', [
        'name' => 'Updated Student',
        'email' => 'updated@example.com',
    ])->assertOk()
        ->assertJsonPath('data.user.name', 'Updated Student')
        ->assertJsonPath('data.user.email', 'updated@example.com');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Student',
        'email' => 'updated@example.com',
    ]);
});
