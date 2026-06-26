<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CourseAdminResource extends JsonResource
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
            'status' => $this->status,
            'publishedAt' => $this->published_at?->toISOString(),
            'thumbnail' => $this->getFirstMediaUrl('thumbnail') ?: null,
            'category' => CategoryAdminResource::make($this->whenLoaded('category')),
            'sectionsCount' => $this->whenCounted('sections'),
            'lessonsCount' => $this->whenCounted('lessons'),
            'accessesCount' => $this->whenCounted('accesses'),
            'sections' => CourseSectionAdminResource::collection($this->whenLoaded('sections')),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
