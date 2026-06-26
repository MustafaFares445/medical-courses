<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Support\Locale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class BookDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = Locale::fromRequest($request);

        return [
            'id' => $this->id,
            'title' => $this->localized('title', $locale),
            'slug' => $this->slug,
            'shortDescription' => $this->localized('short_description', $locale),
            'description' => $this->localized('description', $locale),
            'price' => $this->price,
            'currency' => $this->currency,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'cover' => $this->getFirstMediaUrl('cover') ?: null,
            'hasProtectedFile' => $this->external_file_url !== null || $this->hasMedia('book-file'),
            'publishedAt' => $this->published_at?->toISOString(),
        ];
    }
}
