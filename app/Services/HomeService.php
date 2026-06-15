<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Article;
use App\Models\Book;
use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

final class HomeService
{
    /**
     * @return array{latestCourses: Collection<int, Course>, latestBooks: Collection<int, Book>, latestArticles: Collection<int, Article>}
     */
    public function summaries(int $coursesLimit, int $booksLimit, int $articlesLimit): array
    {
        return [
            'latestCourses' => Course::query()
                ->published()
                ->with('category')
                ->orderByDesc('published_at')
                ->limit($coursesLimit)
                ->get(),
            'latestBooks' => Book::query()
                ->published()
                ->with('category')
                ->orderByDesc('published_at')
                ->limit($booksLimit)
                ->get(),
            'latestArticles' => Article::query()
                ->published()
                ->with('category')
                ->orderByDesc('published_at')
                ->limit($articlesLimit)
                ->get(),
        ];
    }
}
