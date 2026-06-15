<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ProtectedLessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'content' => $this->content,
            'videoUrl' => $this->video_url,
            'videoMediaUrl' => null,
            'courseId' => $this->section?->course_id,
            'sectionId' => $this->course_section_id,
            'sortOrder' => $this->sort_order,
            'previousLesson' => null,
            'nextLesson' => null,
        ];
    }
}
