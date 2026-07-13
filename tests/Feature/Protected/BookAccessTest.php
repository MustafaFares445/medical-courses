<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\BookAccess;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

it('returns a non-empty signed URL for a purchased uploaded book', function (): void {
    Storage::fake('local');

    $user = User::factory()->student()->create();
    $book = Book::factory()->published()->create();
    $book->addMediaFromString('book content')->usingFileName('clinical-book.pdf')->toMediaCollection('book-file');

    BookAccess::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
    Sanctum::actingAs($user);

    $response = $this->getJson("/api/my/books/{$book->id}/access")
        ->assertOk()
        ->assertJsonPath('data.bookId', $book->id)
        ->assertJsonPath('data.accessType', 'signed_url');

    expect($response->json('data.accessUrl'))->toBeString()->not->toBeEmpty();
    expect($response->json('data.expiresAt'))->toBeString()->not->toBeEmpty();
});

it('denies an unpurchased user from protected book access', function (): void {
    $user = User::factory()->student()->create();
    $book = Book::factory()->published()->create();

    Sanctum::actingAs($user);

    $this->getJson("/api/my/books/{$book->id}/access")
        ->assertForbidden();
});

it('requires authentication for protected book access', function (): void {
    $book = Book::factory()->published()->create();

    $this->getJson("/api/my/books/{$book->id}/access")
        ->assertUnauthorized();
});

it('returns not found when a purchased book has no available file', function (): void {
    $user = User::factory()->student()->create();
    $book = Book::factory()->published()->create();

    BookAccess::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
    Sanctum::actingAs($user);

    $this->getJson("/api/my/books/{$book->id}/access")
        ->assertNotFound();
});

it('returns a working signed download URL for an uploaded purchased book', function (): void {
    Storage::fake('local');

    $user = User::factory()->student()->create();
    $book = Book::factory()->published()->create();
    $book
        ->addMediaFromString('book content')
        ->usingFileName('clinical-book.pdf')
        ->toMediaCollection('book-file');

    BookAccess::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
    Sanctum::actingAs($user);

    $accessUrl = $this->getJson("/api/my/books/{$book->id}/access")
        ->assertOk()
        ->json('data.accessUrl');

    expect($accessUrl)->toBeString()->toContain('/api/books/'.$book->id.'/file');

    $parts = parse_url($accessUrl);
    $signedPath = ($parts['path'] ?? '').(isset($parts['query']) ? '?'.$parts['query'] : '');

    $this->get($signedPath)
        ->assertOk()
        ->assertDownload('clinical-book.pdf');
});
