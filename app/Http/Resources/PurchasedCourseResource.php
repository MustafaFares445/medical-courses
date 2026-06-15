<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PurchasedCourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $image = $this->getFirstMediaUrl('thumbnail') ?: null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'shortDescription' => $this->short_description,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'thumbnail' => $image,
            'publishedAt' => $this->published_at?->toISOString(),
        ];
    }
}
