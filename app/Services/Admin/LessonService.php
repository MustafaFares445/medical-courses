<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Data\Admin\LessonData;
use App\Models\CourseSection;
use App\Models\Lesson;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final class LessonService
{
    public function __construct(private readonly MediaReplacementService $mediaReplacementService) {}

    public function create(CourseSection $section, LessonData $data, ?UploadedFile $lessonVideo = null): Lesson
    {
        return DB::transaction(function () use ($section, $data, $lessonVideo): Lesson {
            /** @var Lesson $lesson */
            $lesson = $section->lessons()->create($data->toModelAttributes());

            if ($lessonVideo instanceof UploadedFile) {
                $this->mediaReplacementService->replaceSingleFile($lesson, $lessonVideo, 'lesson-video');
            }

            return $lesson->refresh();
        });
    }

    public function update(Lesson $lesson, LessonData $data, ?UploadedFile $lessonVideo = null): Lesson
    {
        return DB::transaction(function () use ($lesson, $data, $lessonVideo): Lesson {
            $lesson->forceFill($data->toModelAttributes($lesson))->save();

            if ($lessonVideo instanceof UploadedFile) {
                $this->mediaReplacementService->replaceSingleFile($lesson, $lessonVideo, 'lesson-video');
            }

            return $lesson->refresh();
        });
    }

    public function delete(Lesson $lesson): void
    {
        DB::transaction(fn (): ?bool => $lesson->delete());
    }
}
