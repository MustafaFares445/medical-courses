<?php

declare(strict_types=1);

use App\Models\Article;

it('lists published articles and excludes hidden articles', function (): void {
    Article::factory()->published()->create([
        'title' => 'Emergency Medicine Study Guide',
        'slug' => 'emergency-medicine-study-guide',
    ]);
    Article::factory()->hidden()->create([
        'title' => 'Hidden Article',
        'slug' => 'hidden-article',
    ]);

    $this->getJson('/api/articles?search=emergency')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.slug', 'emergency-medicine-study-guide');
});

it('shows published article details', function (): void {
    Article::factory()->published()->create([
        'title' => 'How to Study Emergency Medicine',
        'slug' => 'study-emergency-medicine',
        'body' => 'Published article body.',
    ]);

    $this->getJson('/api/articles/study-emergency-medicine')
        ->assertOk()
        ->assertJsonPath('data.title', 'How to Study Emergency Medicine')
        ->assertJsonPath('data.body', 'Published article body.');
});

it('returns not found for hidden article details', function (): void {
    Article::factory()->hidden()->create(['slug' => 'hidden-article']);

    $this->getJson('/api/articles/hidden-article')->assertNotFound();
});
