<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Support\Locale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ProtectedLessonResource extends JsonResource
{
    private ?string $videoMediaUrl = null;

    public function withVideoMediaUrl(?string $videoMediaUrl): self
    {
        $this->videoMediaUrl = $videoMediaUrl;

        return $this;
    }

    public function toArray(Request $request): array
    {
        $locale = Locale::fromRequest($request);

        return [
            'id' => $this->id,
            'title' => $this->localized('title', $locale),
            'slug' => $this->slug,
            'summary' => $this->localized('summary', $locale),
            'content' => $this->localized('content', $locale),
            'videoUrl' => $this->video_url,
            'videoMediaUrl' => $this->videoMediaUrl,
            'courseId' => $this->section?->course_id,
            'sectionId' => $this->course_section_id,
            'sortOrder' => $this->sort_order,
            'previousLesson' => null,
            'nextLesson' => null,
        ];
    }
}
