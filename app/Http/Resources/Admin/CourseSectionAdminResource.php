<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\CourseSection
 */
final class CourseSectionAdminResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'courseId' => $this->course_id,
            'title' => $this->title,
            'description' => $this->description,
            'sortOrder' => $this->sort_order,
            'lessonsCount' => $this->whenCounted('lessons'),
            'lessons' => LessonAdminResource::collection($this->whenLoaded('lessons')),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
