<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Data\Admin\BookData;
use App\Models\Book;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class BookService
{
    public function __construct(
        private readonly PublishStatusService $publishStatusService,
        private readonly MediaReplacementService $mediaReplacementService,
    ) {}

    public function create(BookData $data, ?UploadedFile $cover = null, ?UploadedFile $bookFile = null): Book
    {
        return DB::transaction(function () use ($data, $cover, $bookFile): Book {
            $book = new Book();
            $attributes = $this->publishStatusService->applyPublishTimestamp($book, $this->attributes($data));
            $this->ensurePublishable($book, $attributes, $bookFile);
            $book->forceFill($attributes)->save();
            $this->replaceMedia($book, $cover, $bookFile);

            return $book->refresh();
        });
    }

    public function update(Book $book, BookData $data, ?UploadedFile $cover = null, ?UploadedFile $bookFile = null): Book
    {
        return DB::transaction(function () use ($book, $data, $cover, $bookFile): Book {
            $attributes = $this->publishStatusService->applyPublishTimestamp($book, $this->attributes($data, $book));
            $this->ensurePublishable($book, $attributes, $bookFile);
            $book->forceFill($attributes)->save();
            $this->replaceMedia($book, $cover, $bookFile);

            return $book->refresh();
        });
    }

    public function delete(Book $book): void
    {
        DB::transaction(fn (): ?bool => $book->delete());
    }

    /** @return array<string, mixed> */
    private function attributes(BookData $data, ?Book $book = null): array
    {
        $attributes = [];

        if ($this->hasField($data, 'categoryId')) {
            $attributes['category_id'] = $data->categoryId;
        }
        if ($this->hasField($data, 'title')) {
            $attributes['title'] = $data->title;
        }
        if ($this->hasField($data, 'slug')) {
            $attributes['slug'] = $data->slug !== null && $data->slug !== '' ? Str::slug($data->slug) : Str::slug((string) $data->title);
        } elseif (! $book instanceof Book && $data->title !== null) {
            $attributes['slug'] = Str::slug($data->title);
        }
        if ($this->hasField($data, 'shortDescription')) {
            $attributes['short_description'] = $data->shortDescription;
        }
        if ($this->hasField($data, 'description')) {
            $attributes['description'] = $data->description;
        }
        if ($this->hasField($data, 'price')) {
            $attributes['price'] = $data->price;
        }
        if ($this->hasField($data, 'currency')) {
            $attributes['currency'] = $data->currency;
        }
        if ($this->hasField($data, 'externalFileUrl')) {
            $attributes['external_file_url'] = $data->externalFileUrl;
        }
        if ($this->hasField($data, 'status')) {
            $attributes['status'] = $data->status;
        }

        return $attributes;
    }

    /** @param array<string, mixed> $attributes */
    private function ensurePublishable(Book $book, array $attributes, ?UploadedFile $bookFile): void
    {
        $status = (string) ($attributes['status'] ?? $book->status);
        $externalUrl = $attributes['external_file_url'] ?? $book->external_file_url;

        if ($status !== 'published') {
            return;
        }

        if ($bookFile instanceof UploadedFile || (is_string($externalUrl) && $externalUrl !== '') || $book->getFirstMedia('book-file') !== null) {
            return;
        }

        throw ValidationException::withMessages([
            'externalFileUrl' => ['Publishing a book requires an external file URL or uploaded book file.'],
        ]);
    }

    private function replaceMedia(Book $book, ?UploadedFile $cover, ?UploadedFile $bookFile): void
    {
        if ($cover instanceof UploadedFile) {
            $this->mediaReplacementService->replaceSingleFile($book, $cover, 'cover');
        }

        if ($bookFile instanceof UploadedFile) {
            $this->mediaReplacementService->replaceSingleFile($book, $bookFile, 'book-file');
        }
    }

    private function hasField(BookData $data, string $field): bool
    {
        return in_array($field, $data->fields, true);
    }
}
