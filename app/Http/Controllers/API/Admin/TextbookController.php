<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Data\Admin\BookData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookFilterRequest;
use App\Http\Requests\Admin\BookRequest;
use App\Http\Resources\Admin\BookAdminResource;
use App\Models\Book;
use App\Services\Admin\BookService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class TextbookController extends Controller
{
    public function index(BookFilterRequest $request): AnonymousResourceCollection
    {
        $query = Book::query()->with('category')->withCount('accesses')->search($request->search());
        if ($request->status() !== null) {
            $query->where('status', $request->status());
        }
        if ($request->categoryId() !== null) {
            $query->where('category_id', $request->categoryId());
        }
        if ($request->priceMin() !== null) {
            $query->where('price', '>=', $request->priceMin());
        }
        if ($request->priceMax() !== null) {
            $query->where('price', '<=', $request->priceMax());
        }
        if ($request->createdAfter() !== null) {
            $query->whereDate('created_at', '>=', $request->createdAfter());
        }
        if ($request->createdBefore() !== null) {
            $query->whereDate('created_at', '<=', $request->createdBefore());
        }

        return BookAdminResource::collection($query->orderBy($request->sortColumnName(), $request->sortDirectionName())->paginate($request->perPage()));
    }

    public function store(BookRequest $request, BookService $service): JsonResponse
    {
        $book = $service->create($this->data($request->validated()), $request->file('cover'), $request->file('bookFile'));

        return BookAdminResource::make($this->loadBook($book))->response()->setStatusCode(201);
    }

    public function show(Book $book): BookAdminResource
    {
        return BookAdminResource::make($this->loadBook($book));
    }

    public function update(BookRequest $request, Book $book, BookService $service): BookAdminResource
    {
        $book = $service->update($book, $this->data($request->validated()), $request->file('cover'), $request->file('bookFile'));

        return BookAdminResource::make($this->loadBook($book));
    }

    public function destroy(Book $book, BookService $service): JsonResponse
    {
        $service->delete($book);

        return ApiResponse::noContent();
    }

    public function file(Request $request, Book $book): StreamedResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        $media = $book->getFirstMedia('book-file');
        abort_if($media === null, 404, 'Book file is not available.');

        return Storage::disk($media->disk)->download($media->getPathRelativeToRoot(), $media->file_name);
    }

    private function data(array $validated): BookData
    {
        return new BookData(
            categoryId: array_key_exists('categoryId', $validated) && $validated['categoryId'] !== null ? (int) $validated['categoryId'] : null,
            title: is_array($validated['title'] ?? null) ? $validated['title'] : null,
            slug: is_string($validated['slug'] ?? null) ? $validated['slug'] : null,
            shortDescription: array_key_exists('shortDescription', $validated) && is_array($validated['shortDescription']) ? $validated['shortDescription'] : null,
            description: array_key_exists('description', $validated) && is_array($validated['description']) ? $validated['description'] : null,
            price: array_key_exists('price', $validated) ? (string) $validated['price'] : null,
            currency: is_string($validated['currency'] ?? null) ? strtoupper($validated['currency']) : null,
            status: is_string($validated['status'] ?? null) ? $validated['status'] : null,
            fields: array_keys($validated),
        );
    }

    private function loadBook(Book $book): Book
    {
        return $book->load('category')->loadCount('accesses');
    }
}
