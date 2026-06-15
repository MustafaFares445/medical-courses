<?php

declare(strict_types=1);

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('denies guests from admin articles', function (): void {
    $this->getJson('/api/admin/articles')->assertUnauthorized();
});

it('denies students from admin articles', function (): void {
    Sanctum::actingAs(User::factory()->student()->create());

    $this->getJson('/api/admin/articles')->assertForbidden();
});

it('lists articles with filters and camel case fields', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $category = Category::factory()->article()->create();
    Article::factory()->for($category)->published()->create(['title' => 'How to Study Emergency Medicine', 'slug' => 'how-to-study-emergency-medicine']);
    Article::factory()->for($category)->hidden()->create(['title' => 'Hidden Article', 'slug' => 'hidden-article']);

    $this->getJson("/api/admin/articles?filter[status]=published&filter[categoryId]={$category->id}&search=Emergency")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'How to Study Emergency Medicine')
        ->assertJsonStructure([
            'data' => [[
                'id',
                'categoryId',
                'title',
                'slug',
                'excerpt',
                'body',
                'status',
                'publishedAt',
                'articleImage',
                'createdAt',
                'updatedAt',
            ]],
            'links',
            'meta',
        ]);
});

it('creates an article and sets published timestamp', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $category = Category::factory()->article()->create();

    $this->postJson('/api/admin/articles', [
        'categoryId' => $category->id,
        'title' => 'How to Study Emergency Medicine',
        'excerpt' => 'Short article excerpt',
        'body' => 'Full article body',
        'status' => 'published',
    ])->assertCreated()
        ->assertJsonPath('data.title', 'How to Study Emergency Medicine')
        ->assertJsonPath('data.slug', 'how-to-study-emergency-medicine')
        ->assertJsonPath('data.status', 'published');

    expect(Article::query()->where('slug', 'how-to-study-emergency-medicine')->first()?->published_at)->not->toBeNull();
});

it('updates an article', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $article = Article::factory()->create([
        'title' => 'Old Article',
        'slug' => 'old-article',
        'status' => 'draft',
    ]);

    $this->patchJson("/api/admin/articles/{$article->id}", [
        'title' => 'Updated Article',
        'slug' => 'updated-article',
        'body' => 'Updated body',
        'status' => 'hidden',
    ])->assertOk()
        ->assertJsonPath('data.title', 'Updated Article')
        ->assertJsonPath('data.slug', 'updated-article')
        ->assertJsonPath('data.status', 'hidden');

    $this->assertDatabaseHas('articles', [
        'id' => $article->id,
        'title' => 'Updated Article',
        'slug' => 'updated-article',
        'status' => 'hidden',
    ]);
});

it('validates article payloads', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $this->postJson('/api/admin/articles', [
        'title' => '',
        'body' => '',
        'status' => 'invalid',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'status']);
});

it('requires a body when publishing an article', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $this->postJson('/api/admin/articles', [
        'title' => 'Published Without Body',
        'status' => 'published',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['body']);
});

it('soft deletes articles', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $article = Article::factory()->published()->create();

    $this->deleteJson("/api/admin/articles/{$article->id}")->assertNoContent();

    $this->assertSoftDeleted('articles', ['id' => $article->id]);
});
