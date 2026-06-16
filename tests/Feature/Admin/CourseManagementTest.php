<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('denies guests from admin courses', function (): void {
    $this->getJson('/api/admin/courses')->assertUnauthorized();
});

it('denies students from admin courses', function (): void {
    Sanctum::actingAs(User::factory()->student()->create());

    $this->getJson('/api/admin/courses')->assertForbidden();
});

it('lists courses with filters and camel case fields', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $category = Category::factory()->course()->create();
    Course::factory()->for($category)->published()->create(['title' => 'Emergency Medicine Basics', 'slug' => 'emergency-medicine-basics']);
    Course::factory()->for($category)->hidden()->create(['title' => 'Hidden Surgery', 'slug' => 'hidden-surgery']);

    $this->getJson("/api/admin/courses?filter[status]=published&filter[categoryId]={$category->id}&search=Emergency")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Emergency Medicine Basics')
        ->assertJsonStructure([
            'data' => [[
                'id',
                'categoryId',
                'title',
                'slug',
                'shortDescription',
                'description',
                'price',
                'currency',
                'status',
                'publishedAt',
                'thumbnail',
                'createdAt',
                'updatedAt',
            ]],
            'links',
            'meta',
        ]);
});

it('creates a course and sets published timestamp', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $category = Category::factory()->course()->create();

    $this->postJson('/api/admin/courses', [
        'categoryId' => $category->id,
        'title' => 'Clinical Cardiology',
        'shortDescription' => 'Short course summary',
        'description' => 'Full course details',
        'price' => '49.00',
        'currency' => 'usd',
        'status' => 'published',
    ])->assertCreated()
        ->assertJsonPath('data.title', 'Clinical Cardiology')
        ->assertJsonPath('data.slug', 'clinical-cardiology')
        ->assertJsonPath('data.status', 'published')
        ->assertJsonPath('data.currency', 'USD');

    $this->assertDatabaseHas('courses', [
        'category_id' => $category->id,
        'slug' => 'clinical-cardiology',
        'status' => 'published',
        'currency' => 'USD',
    ]);

    expect(Course::query()->where('slug', 'clinical-cardiology')->first()?->published_at)->not->toBeNull();
});

it('updates a course', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $course = Course::factory()->create([
        'title' => 'Old Course',
        'slug' => 'old-course',
        'status' => 'draft',
    ]);

    $this->patchJson("/api/admin/courses/{$course->id}", [
        'title' => 'Updated Course',
        'slug' => 'updated-course',
        'price' => '79.00',
        'currency' => 'USD',
        'status' => 'hidden',
    ])->assertOk()
        ->assertJsonPath('data.title', 'Updated Course')
        ->assertJsonPath('data.slug', 'updated-course')
        ->assertJsonPath('data.status', 'hidden');

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'title' => 'Updated Course',
        'slug' => 'updated-course',
        'status' => 'hidden',
    ]);
});

it('validates course payloads', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $this->postJson('/api/admin/courses', [
        'title' => '',
        'price' => -1,
        'currency' => 'US',
        'status' => 'invalid',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'price', 'currency', 'status']);
});

it('soft deletes courses', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $course = Course::factory()->create();

    $this->deleteJson("/api/admin/courses/{$course->id}")->assertNoContent();

    $this->assertSoftDeleted('courses', ['id' => $course->id]);
});
