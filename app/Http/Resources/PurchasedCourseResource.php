<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Lesson;
use App\Support\Locale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PurchasedCourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = Locale::fromRequest($request);
        $image = $this->getFirstMediaUrl('thumbnail') ?: null;
        $firstLesson = $this->sections
            ->flatMap(fn ($section) => $section->lessons)
            ->first();

        return [
            'id' => $this->id,
            'title' => $this->localized('title', $locale),
            'slug' => $this->slug,
            'shortDescription' => $this->localized('short_description', $locale),
            'description' => $this->localized('description', $locale),
            'price' => $this->price,
            'currency' => $this->currency,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'thumbnail' => $image,
            'sections' => $this->sections->map(fn ($section) => [
                'id' => $section->id,
                'title' => $section->localized('title', $locale),
                'description' => $section->localized('description', $locale),
                'sortOrder' => $section->sort_order,
                'lessons' => $section->lessons->map(fn (Lesson $lesson) => [
                    'id' => $lesson->id,
                    'title' => $lesson->localized('title', $locale),
                    'slug' => $lesson->slug,
                    'summary' => $lesson->localized('summary', $locale),
                    'sortOrder' => $lesson->sort_order,
                    'href' => sprintf('/learn/courses/%s/lessons/%s', $this->id, $lesson->id),
                ])->values(),
            ])->values(),
            'firstLesson' => $firstLesson ? [
                'id' => $firstLesson->id,
                'title' => $firstLesson->localized('title', $locale),
                'href' => sprintf('/learn/courses/%s/lessons/%s', $this->id, $firstLesson->id),
            ] : null,
        ];
    }
}
