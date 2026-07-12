<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Lesson
 */
final class LessonAdminResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'courseSectionId' => $this->course_section_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'content' => $this->content,
            'videoUrl' => $this->video_url,
            'lessonVideo' => $this->getFirstMedia('lesson-video') !== null
                ? route('admin.lessons.video', ['lesson' => $this->id])
                : null,
            'sortOrder' => $this->sort_order,
            'status' => $this->status,
            'section' => CourseSectionAdminResource::make($this->whenLoaded('section')),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
