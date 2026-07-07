<?php

declare(strict_types=1);

use App\Models\Book;

it('returns published books sorted by publishedAt without exposing protected files', function (): void {
    Book::factory()->published()->create([
        'title' => ['en' => 'Clinical Anatomy', 'ar' => null],
        'slug' => 'clinical-anatomy',
        'external_file_url' => 'https://files.example.com/secret.pdf',
    ]);

    $this->getJson('/api/books?page=1&perPage=8&sort=-publishedAt')
        ->assertOk()
        ->assertJsonPath('data.0.title', 'Clinical Anatomy')
        ->assertJsonMissingPath('data.0.externalFileUrl')
        ->assertJsonMissingPath('data.0.accessUrl');
});
