<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Support\Locale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ArticleListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = Locale::fromRequest($request);

        return [
            'id' => $this->id,
            'title' => $this->localized('title', $locale),
            'slug' => $this->slug,
            'excerpt' => $this->localized('excerpt', $locale),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'image' => $this->getFirstMediaUrl('article-image') ?: null,
            'publishedAt' => $this->published_at?->toISOString(),
        ];
    }
}
