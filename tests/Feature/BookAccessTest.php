<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('denies users who did not buy the book before attempting file access', function (): void {
    $user = User::factory()->student()->create();
    $book = Book::factory()->published()->create();

    Sanctum::actingAs($user);

    $this->getJson("/api/my/books/{$book->id}/access")->assertForbidden();
});
