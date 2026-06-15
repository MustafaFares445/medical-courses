<?php

declare(strict_types=1);

use App\Models\Book;

it('lists published books and excludes hidden books', function (): void {
    Book::factory()->published()->create([
        'title' => 'Clinical Anatomy Handbook',
        'slug' => 'clinical-anatomy-handbook',
    ]);
    Book::factory()->hidden()->create([
        'title' => 'Hidden Book',
        'slug' => 'hidden-book',
    ]);

    $this->getJson('/api/books?search=anatomy')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.slug', 'clinical-anatomy-handbook');
});

it('shows published book details without protected file link', function (): void {
    Book::factory()->published()->create([
        'slug' => 'clinical-book',
        'external_file_url' => 'https://files.example.test/private-book.pdf',
    ]);

    $this->getJson('/api/books/clinical-book')
        ->assertOk()
        ->assertJsonPath('data.hasProtectedFile', true)
        ->assertJsonMissing(['externalFileUrl' => 'https://files.example.test/private-book.pdf'])
        ->assertJsonMissing(['accessUrl' => 'https://files.example.test/private-book.pdf']);
});

it('returns not found for hidden book details', function (): void {
    Book::factory()->hidden()->create(['slug' => 'hidden-book']);

    $this->getJson('/api/books/hidden-book')->assertNotFound();
});
