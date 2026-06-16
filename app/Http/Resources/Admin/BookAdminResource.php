<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Book
 */
final class BookAdminResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'categoryId' => $this->category_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'shortDescription' => $this->short_description,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'externalFileUrl' => $this->external_file_url,
            'status' => $this->status,
            'publishedAt' => $this->published_at?->toISOString(),
            'cover' => $this->getFirstMediaUrl('cover') ?: null,
            'hasBookFile' => $this->getFirstMedia('book-file') !== null,
            'category' => CategoryAdminResource::make($this->whenLoaded('category')),
            'accessesCount' => $this->whenCounted('accesses'),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
