<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookAccessResource;
use App\Models\Book;
use App\Models\User;
use App\Services\BookAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function download(Request $request, Book $book): StreamedResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        $media = $book->getFirstMedia('book-file');
        abort_if($media === null, 404, 'Book file is not available.');

        $disk = Storage::disk($media->disk);
        abort_unless($disk->exists($media->getPathRelativeToRoot()), 404, 'Book file is not available.');

        return $disk->download($media->getPathRelativeToRoot(), $media->file_name);
    }
}
