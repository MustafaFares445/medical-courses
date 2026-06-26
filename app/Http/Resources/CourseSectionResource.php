<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Support\Locale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CourseSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = Locale::fromRequest($request);

        return [
            'id' => $this->id,
            'title' => $this->localized('title', $locale),
            'description' => $this->localized('description', $locale),
            'sortOrder' => $this->sort_order,
            'lessons' => LessonPublicResource::collection($this->whenLoaded('lessons')),
        ];
    }
}
