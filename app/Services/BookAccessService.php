<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Book;
use App\Models\BookAccess;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

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

        $media = $book->getFirstMedia('book-file');

        abort_if($media === null, 404, 'Book file is not available.');

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
