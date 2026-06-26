<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class BookAdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'categoryId' => $this->category_id,
            'title' => $this->translations('title'),
            'slug' => $this->slug,
            'shortDescription' => $this->translations('short_description'),
            'description' => $this->translations('description'),
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
