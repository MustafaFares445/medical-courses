<?php

declare(strict_types=1);

use App\Models\Book;

it('does not expose protected book URLs in public book detail responses', function (): void {
    $book = Book::factory()->published()->create([
        'title' => ['en' => 'Clinical Anatomy Handbook', 'ar' => null],
        'slug' => 'clinical-anatomy-handbook',
        'external_file_url' => 'https://secure.example.com/file.pdf',
    ]);

    $this->getJson("/api/books/{$book->slug}")
        ->assertOk()
        ->assertJsonPath('data.title', 'Clinical Anatomy Handbook')
        ->assertJsonPath('data.hasProtectedFile', true)
        ->assertJsonMissingPath('data.externalFileUrl')
        ->assertJsonMissingPath('data.bookFileUrl')
        ->assertJsonMissingPath('data.accessUrl');
});

it('does not expose protected book URLs in public book list responses', function (): void {
    Book::factory()->published()->create([
        'title' => ['en' => 'Clinical Anatomy Handbook', 'ar' => null],
        'slug' => 'clinical-anatomy-handbook',
        'external_file_url' => 'https://secure.example.com/file.pdf',
    ]);

    $this->getJson('/api/books')
        ->assertOk()
        ->assertJsonPath('data.0.title', 'Clinical Anatomy Handbook')
        ->assertJsonMissingPath('data.0.externalFileUrl')
        ->assertJsonMissingPath('data.0.bookFileUrl')
        ->assertJsonMissingPath('data.0.accessUrl');
});
