<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class BookListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $image = $this->getFirstMediaUrl('cover') ?: null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'shortDescription' => $this->short_description,
            'price' => $this->price,
            'currency' => $this->currency,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'cover' => $image,
            'publishedAt' => $this->published_at?->toISOString(),
        ];
    }
}
