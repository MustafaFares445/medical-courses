<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\BookAccess;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns the authenticated user purchased courses and books', function (): void {
    $user = User::factory()->student()->create();
    $otherUser = User::factory()->student()->create();

    $course = Course::factory()->published()->create(['title' => 'Purchased Course']);
    $book = Book::factory()->published()->create(['title' => 'Purchased Book']);
    $otherCourse = Course::factory()->published()->create(['title' => 'Other Course']);

    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);
    BookAccess::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
    CourseAccess::factory()->create(['user_id' => $otherUser->id, 'course_id' => $otherCourse->id]);

    Sanctum::actingAs($user);

    $this->getJson('/api/my/library')
        ->assertOk()
        ->assertJsonCount(1, 'data.courses')
        ->assertJsonCount(1, 'data.books')
        ->assertJsonPath('data.courses.0.title', 'Purchased Course')
        ->assertJsonPath('data.books.0.title', 'Purchased Book')
        ->assertJsonMissing(['title' => 'Other Course']);
});

it('requires authentication for the library', function (): void {
    $this->getJson('/api/my/library')->assertUnauthorized();
});
