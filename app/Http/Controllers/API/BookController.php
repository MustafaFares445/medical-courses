<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookFilterRequest;
use App\Http\Resources\BookDetailResource;
use App\Http\Resources\BookListResource;
use App\Models\Book;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class BookController extends Controller
{
    public function index(BookFilterRequest $request): AnonymousResourceCollection
    {
        $query = Book::query()
            ->published()
            ->with('category')
            ->search($request->search())
            ->category($request->categoryId());

        if ($request->categorySlug() !== null) {
            $query->whereHas('category', fn ($category) => $category->where('slug', $request->categorySlug()));
        }

        if ($request->priceMin() !== null) {
            $query->where('price', '>=', $request->priceMin());
        }

        if ($request->priceMax() !== null) {
            $query->where('price', '<=', $request->priceMax());
        }

        return BookListResource::collection(
            $query->orderBy($request->sortColumn(), $request->sortDirection())
                ->paginate($request->perPage())
        );
    }

    public function show(Book $book): BookDetailResource
    {
        abort_unless($book->status === 'published', 404);

        $book->load('category');

        return BookDetailResource::make($book);
    }
}
