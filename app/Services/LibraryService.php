<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Book;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class LibraryService
{
    /** @return array{courses: Collection<int, Course>, books: Collection<int, Book>} */
    public function forUser(User $user): array
    {
        $courses = Course::query()
            ->whereHas('accesses', fn ($query) => $query->where('user_id', $user->id))
            ->with('category')
            ->orderBy('title')
            ->get();

        $books = Book::query()
            ->whereHas('accesses', fn ($query) => $query->where('user_id', $user->id))
            ->with('category')
            ->orderBy('title')
            ->get();

        return [
            'courses' => $courses,
            'books' => $books,
        ];
    }
}
