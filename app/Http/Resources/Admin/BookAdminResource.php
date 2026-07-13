<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

final class BookAdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        [$bookFileUrl, $bookFileUrlExpiresAt] = $this->temporaryBookFileAccess();

        return [
            'id' => $this->id,
            'categoryId' => $this->category_id,
            'title' => $this->translations('title'),
            'slug' => $this->slug,
            'shortDescription' => $this->translations('short_description'),
            'description' => $this->translations('description'),
            'price' => $this->price,
            'currency' => $this->currency,
            'status' => $this->status,
            'publishedAt' => $this->published_at?->toISOString(),
            'cover' => $this->getFirstMediaUrl('cover') ?: null,
            'hasBookFile' => $this->hasMedia('book-file'),
            'bookFileUrl' => $bookFileUrl,
            'bookFileUrlExpiresAt' => $bookFileUrlExpiresAt,
            'category' => CategoryAdminResource::make($this->whenLoaded('category')),
            'accessesCount' => $this->whenCounted('accesses'),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }

    /** @return array{0: ?string, 1: ?string} */
    private function temporaryBookFileAccess(): array
    {
        if (! $this->hasMedia('book-file')) {
            return [null, null];
        }

        $expiresAt = Carbon::now()->addMinutes(15);

        return [
            URL::temporarySignedRoute('admin.books.file', $expiresAt, ['book' => $this->id]),
            $expiresAt->toISOString(),
        ];
    }
}
