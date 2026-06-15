<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CourseDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'shortDescription' => $this->short_description,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'thumbnail' => null,
            'sections' => CourseSectionResource::collection($this->whenLoaded('sections')),
            'publishedAt' => $this->published_at?->toISOString(),
        ];
    }
}
