<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\BookAccess;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
    Book::factory()->for($category)->published()->create([
        'title' => ['en' => 'Clinical Anatomy Handbook', 'ar' => null],
        'slug' => 'clinical-anatomy-handbook',
    ]);
    Book::factory()->for($category)->hidden()->create([
        'title' => ['en' => 'Hidden Book', 'ar' => null],
        'slug' => 'hidden-book',
    ]);

    $this->getJson("/api/admin/books?filter[status]=published&filter[categoryId]={$category->id}&search=Anatomy")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title.en', 'Clinical Anatomy Handbook')
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
                'cover',
                'hasBookFile',
                'bookFileUrl',
                'bookFileUrlExpiresAt',
                'createdAt',
                'updatedAt',
            ]],
            'links',
            'meta',
        ]);
});

it('creates a published book with an uploaded protected book file and returns an admin file url', function (): void {
    Storage::fake('local');
    Sanctum::actingAs(User::factory()->admin()->create());

    $category = Category::factory()->book()->create();

    $response = $this->post('/api/admin/books', [
        'categoryId' => $category->id,
        'title' => ['en' => 'Clinical Anatomy Handbook', 'ar' => null],
        'shortDescription' => ['en' => 'Short book summary', 'ar' => null],
        'description' => ['en' => 'Full book details', 'ar' => null],
        'price' => '29.00',
        'currency' => 'USD',
        'status' => 'published',
        'bookFile' => UploadedFile::fake()->create('clinical-anatomy.pdf', 128, 'application/pdf'),
    ], ['Accept' => 'application/json']);

    $response->assertCreated()
        ->assertJsonPath('data.hasBookFile', true)
        ->assertJsonStructure(['data' => ['bookFileUrl', 'bookFileUrlExpiresAt']]);

    $url = $response->json('data.bookFileUrl');
    expect($url)->toBeString()->and($url)->toContain('/api/admin/books/');

    $path = (string) parse_url($url, PHP_URL_PATH);
    $query = (string) parse_url($url, PHP_URL_QUERY);

    $this->get($path.'?'.$query)->assertOk();
});

it('requires a local book file when creating a book', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $this->postJson('/api/admin/books', [
        'title' => ['en' => 'No File Book', 'ar' => null],
        'price' => '29.00',
        'currency' => 'USD',
        'status' => 'draft',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['bookFile']);
});

it('updates a book', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $book = Book::factory()->create([
        'title' => ['en' => 'Old Book', 'ar' => null],
        'slug' => 'old-book',
        'status' => 'draft',
    ]);
    $book->addMediaFromString('book content')->usingFileName('old-book.pdf')->toMediaCollection('book-file');

    $this->patchJson("/api/admin/books/{$book->id}", [
        'title' => ['en' => 'Updated Book', 'ar' => null],
        'slug' => 'updated-book',
        'price' => '39.00',
        'currency' => 'USD',
        'status' => 'hidden',
    ])->assertOk()
        ->assertJsonPath('data.title.en', 'Updated Book')
        ->assertJsonPath('data.slug', 'updated-book')
        ->assertJsonPath('data.status', 'hidden');
});

it('validates book payloads', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $this->postJson('/api/admin/books', [
        'title' => '',
        'price' => -1,
        'currency' => 'US',
        'status' => 'invalid',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'price', 'currency', 'bookFile', 'status']);
});

it('soft deletes purchased books', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $book = Book::factory()->published()->create();
    BookAccess::factory()->for($book)->create();

    $this->deleteJson("/api/admin/books/{$book->id}")->assertNoContent();

    $this->assertSoftDeleted('books', ['id' => $book->id]);
});
