<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('denies guests from admin routes', function (): void {
    $this->getJson('/api/admin/health')
        ->assertUnauthorized()
        ->assertJsonPath('message', 'Unauthenticated.');
});

it('denies student users from admin routes', function (): void {
    Sanctum::actingAs(User::factory()->student()->create());

    $this->getJson('/api/admin/health')
        ->assertForbidden()
        ->assertJsonPath('message', 'Forbidden.');
});

it('allows admin users to access admin routes', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $this->getJson('/api/admin/health')
        ->assertOk()
        ->assertJsonPath('data.status', 'admin');
});
