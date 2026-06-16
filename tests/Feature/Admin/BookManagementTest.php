<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\BookAccess;
use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('denies guests from admin books', function (): void {
    $this->getJson('/api/admin/books')->assertUnauthorized();
});

it('denies students from admin books', function (): void {
    Sanctum::actingAs(User::factory()->student()->create());

    $this->getJson('/api/admin/books')->assertForbidden();
});

it('lists books with filters and camel case fields', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $category = Category::factory()->book()->create();
    Book::factory()->for($category)->published()->create(['title' => 'Clinical Anatomy Handbook', 'slug' => 'clinical-anatomy-handbook']);
    Book::factory()->for($category)->hidden()->create(['title' => 'Hidden Book', 'slug' => 'hidden-book']);

    $this->getJson("/api/admin/books?filter[status]=published&filter[categoryId]={$category->id}&search=Anatomy")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Clinical Anatomy Handbook')
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
                'externalFileUrl',
                'status',
                'publishedAt',
                'cover',
                'hasBookFile',
                'createdAt',
                'updatedAt',
            ]],
            'links',
            'meta',
        ]);
});

it('creates a published book with a protected external file url', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $category = Category::factory()->book()->create();

    $this->postJson('/api/admin/books', [
        'categoryId' => $category->id,
        'title' => 'Clinical Anatomy Handbook',
        'shortDescription' => 'Short book summary',
        'description' => 'Full book details',
        'price' => '29.00',
        'currency' => 'usd',
        'externalFileUrl' => 'https://secure.example.com/file.pdf',
        'status' => 'published',
    ])->assertCreated()
        ->assertJsonPath('data.title', 'Clinical Anatomy Handbook')
        ->assertJsonPath('data.slug', 'clinical-anatomy-handbook')
        ->assertJsonPath('data.status', 'published')
        ->assertJsonPath('data.currency', 'USD');

    expect(Book::query()->where('slug', 'clinical-anatomy-handbook')->first()?->published_at)->not->toBeNull();
});

it('requires file access information before publishing a book', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $this->postJson('/api/admin/books', [
        'title' => 'No File Book',
        'price' => '29.00',
        'currency' => 'USD',
        'status' => 'published',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['externalFileUrl']);
});

it('updates a book', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $book = Book::factory()->create([
        'title' => 'Old Book',
        'slug' => 'old-book',
        'status' => 'draft',
    ]);

    $this->patchJson("/api/admin/books/{$book->id}", [
        'title' => 'Updated Book',
        'slug' => 'updated-book',
        'price' => '39.00',
        'currency' => 'USD',
        'externalFileUrl' => 'https://secure.example.com/updated.pdf',
        'status' => 'hidden',
    ])->assertOk()
        ->assertJsonPath('data.title', 'Updated Book')
        ->assertJsonPath('data.slug', 'updated-book')
        ->assertJsonPath('data.status', 'hidden');
});

it('validates book payloads', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $this->postJson('/api/admin/books', [
        'title' => '',
        'price' => -1,
        'currency' => 'US',
        'externalFileUrl' => 'not-a-url',
        'status' => 'invalid',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'price', 'currency', 'externalFileUrl', 'status']);
});

it('soft deletes purchased books', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $book = Book::factory()->published()->create();
    BookAccess::factory()->for($book)->create();

    $this->deleteJson("/api/admin/books/{$book->id}")->assertNoContent();

    $this->assertSoftDeleted('books', ['id' => $book->id]);
});
