<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\BookAccess;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('allows a purchased user to access a protected external book link', function (): void {
    $user = User::factory()->student()->create();
    $book = Book::factory()->published()->create([
        'title' => 'Clinical Book',
        'external_file_url' => 'https://files.example.test/clinical-book.pdf',
    ]);

    BookAccess::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
    Sanctum::actingAs($user);

    $this->getJson("/api/my/books/{$book->id}/access")
        ->assertOk()
        ->assertJsonPath('data.bookId', $book->id)
        ->assertJsonPath('data.accessType', 'external_url')
        ->assertJsonPath('data.accessUrl', 'https://files.example.test/clinical-book.pdf');
});

it('denies an unpurchased user from protected book access', function (): void {
    $user = User::factory()->student()->create();
    $book = Book::factory()->published()->create([
        'external_file_url' => 'https://files.example.test/clinical-book.pdf',
    ]);

    Sanctum::actingAs($user);

    $this->getJson("/api/my/books/{$book->id}/access")
        ->assertForbidden();
});

it('requires authentication for protected book access', function (): void {
    $book = Book::factory()->published()->create([
        'external_file_url' => 'https://files.example.test/clinical-book.pdf',
    ]);

    $this->getJson("/api/my/books/{$book->id}/access")
        ->assertUnauthorized();
});

it('returns not found when a purchased book has no available file', function (): void {
    $user = User::factory()->student()->create();
    $book = Book::factory()->published()->create(['external_file_url' => null]);

    BookAccess::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
    Sanctum::actingAs($user);

    $this->getJson("/api/my/books/{$book->id}/access")
        ->assertNotFound();
});
