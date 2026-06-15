<?php

declare(strict_types=1);

use App\Models\Article;
use App\Models\Book;
use App\Models\Course;

it('returns latest published home summaries only', function (): void {
    Course::factory()->published()->create(['title' => 'Published Course']);
    Course::factory()->hidden()->create(['title' => 'Hidden Course']);
    Book::factory()->published()->create(['title' => 'Published Book']);
    Book::factory()->hidden()->create(['title' => 'Hidden Book']);
    Article::factory()->published()->create(['title' => 'Published Article']);
    Article::factory()->hidden()->create(['title' => 'Hidden Article']);

    $this->getJson('/api/home')
        ->assertOk()
        ->assertJsonCount(1, 'data.latestCourses')
        ->assertJsonCount(1, 'data.latestBooks')
        ->assertJsonCount(1, 'data.latestArticles')
        ->assertJsonPath('data.latestCourses.0.title', 'Published Course')
        ->assertJsonPath('data.latestBooks.0.title', 'Published Book')
        ->assertJsonPath('data.latestArticles.0.title', 'Published Article');
});
