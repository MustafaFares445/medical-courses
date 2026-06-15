<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookAccessResource;
use App\Models\Book;
use App\Models\User;
use App\Services\BookAccessService;
use Illuminate\Http\Request;
use RuntimeException;

final class BookAccessController extends Controller
{
    public function __construct(private readonly BookAccessService $books) {}

    public function show(Request $request, Book $book): BookAccessResource
    {
        /** @var User $user */
        $user = $request->user();

        try {
            return BookAccessResource::make($this->books->accessFor($user, $book));
        } catch (RuntimeException) {
            abort(404, 'Book file is not available.');
        }
    }
}
