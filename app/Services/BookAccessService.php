<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Book;
use App\Models\BookAccess;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use RuntimeException;

final class BookAccessService
{
    /** @return array<string, mixed> */
    public function accessFor(User $user, Book $book): array
    {
        $hasAccess = BookAccess::query()
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->exists();

        if (! $hasAccess) {
            abort(403);
        }

        if ($book->external_file_url !== null) {
            return [
                'bookId' => $book->id,
                'title' => $book->title,
                'accessType' => 'external_url',
                'accessUrl' => $book->external_file_url,
                'expiresAt' => null,
            ];
        }

        $media = $book->getFirstMedia('book-file');

        if ($media === null) {
            throw new RuntimeException('Book file is missing.');
        }

        $expiresAt = Carbon::now()->addMinutes(15);

        return [
            'bookId' => $book->id,
            'title' => $book->title,
            'accessType' => 'signed_url',
            'accessUrl' => URL::temporarySignedRoute('books.file', $expiresAt, ['book' => $book->id]),
            'expiresAt' => $expiresAt->toISOString(),
        ];
    }
}
