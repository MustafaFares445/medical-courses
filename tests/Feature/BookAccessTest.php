<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\BookAccess;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns a file link for a purchased book', function (): void {
    $user = User::factory()->student()->create();
    $book = Book::factory()->published()->create([
        'title' => ['en' => 'Clinical Anatomy Handbook', 'ar' => null],
        'external_file_url' => 'https://files.example.test/book.pdf',
    ]);
    BookAccess::factory()->for($user)->for($book)->create();

    Sanctum::actingAs($user);

    $this->getJson("/api/my/books/{$book->id}/access")
        ->assertOk()
        ->assertJsonPath('data.bookId', $book->id)
        ->assertJsonPath('data.accessType', 'external_url')
        ->assertJsonPath('data.accessUrl', 'https://files.example.test/book.pdf');
});

it('denies users who did not buy the book', function (): void {
    $user = User::factory()->student()->create();
    $book = Book::factory()->published()->create([
        'external_file_url' => 'https://files.example.test/book.pdf',
    ]);

    Sanctum::actingAs($user);

    $this->getJson("/api/my/books/{$book->id}/access")->assertForbidden();
});
