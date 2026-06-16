<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('denies guests from admin categories', function (): void {
    $this->getJson('/api/admin/categories')->assertUnauthorized();
});

it('denies students from admin categories', function (): void {
    Sanctum::actingAs(User::factory()->student()->create());

    $this->getJson('/api/admin/categories')->assertForbidden();
});

it('lists categories with filters and camel case fields', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    Category::factory()->course()->create(['name' => 'Cardiology', 'slug' => 'cardiology']);
    Category::factory()->book()->inactive()->create(['name' => 'Anatomy Books', 'slug' => 'anatomy-books']);

    $this->getJson('/api/admin/categories?filter[type]=course&search=cardiology')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Cardiology')
        ->assertJsonStructure([
            'data' => [[
                'id',
                'type',
                'name',
                'slug',
                'description',
                'isActive',
                'createdAt',
                'updatedAt',
            ]],
            'links',
            'meta',
        ]);
});

it('creates a category', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $this->postJson('/api/admin/categories', [
        'type' => 'course',
        'name' => 'Emergency Medicine',
        'description' => 'Clinical emergency topics.',
        'isActive' => true,
    ])->assertCreated()
        ->assertJsonPath('data.name', 'Emergency Medicine')
        ->assertJsonPath('data.slug', 'emergency-medicine')
        ->assertJsonPath('data.isActive', true);

    $this->assertDatabaseHas('categories', [
        'type' => 'course',
        'slug' => 'emergency-medicine',
        'is_active' => true,
    ]);
});

it('updates a category', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $category = Category::factory()->course()->create([
        'name' => 'Old Name',
        'slug' => 'old-name',
    ]);

    $this->patchJson("/api/admin/categories/{$category->id}", [
        'name' => 'Updated Name',
        'slug' => 'updated-name',
        'isActive' => false,
    ])->assertOk()
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.slug', 'updated-name')
        ->assertJsonPath('data.isActive', false);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Updated Name',
        'slug' => 'updated-name',
        'is_active' => false,
    ]);
});

it('validates category payloads', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $this->postJson('/api/admin/categories', [
        'type' => 'invalid',
        'name' => '',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['type', 'name']);
});

it('prevents deleting categories assigned to content', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $category = Category::factory()->course()->create();
    Course::factory()->for($category)->create();

    $this->deleteJson("/api/admin/categories/{$category->id}")->assertStatus(409);

    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});

it('deletes unused categories', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $category = Category::factory()->course()->create();

    $this->deleteJson("/api/admin/categories/{$category->id}")->assertNoContent();

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});
