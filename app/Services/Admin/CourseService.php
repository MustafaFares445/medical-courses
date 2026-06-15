<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Data\Admin\CourseData;
use App\Models\Course;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final class CourseService
{
    public function __construct(
        private readonly PublishStatusService $publishStatusService,
        private readonly MediaReplacementService $mediaReplacementService,
    ) {}

    public function create(CourseData $data, ?UploadedFile $thumbnail = null): Course
    {
        return DB::transaction(function () use ($data, $thumbnail): Course {
            $course = new Course();
            $attributes = $this->publishStatusService->applyPublishTimestamp($course, $data->toModelAttributes());
            $course->forceFill($attributes)->save();

            if ($thumbnail instanceof UploadedFile) {
                $this->mediaReplacementService->replaceSingleFile($course, $thumbnail, 'thumbnail');
            }

            return $course->refresh();
        });
    }

    public function update(Course $course, CourseData $data, ?UploadedFile $thumbnail = null): Course
    {
        return DB::transaction(function () use ($course, $data, $thumbnail): Course {
            $attributes = $this->publishStatusService->applyPublishTimestamp($course, $data->toModelAttributes($course));
            $course->forceFill($attributes)->save();

            if ($thumbnail instanceof UploadedFile) {
                $this->mediaReplacementService->replaceSingleFile($course, $thumbnail, 'thumbnail');
            }

            return $course->refresh();
        });
    }

    public function delete(Course $course): void
    {
        DB::transaction(fn (): ?bool => $course->delete());
    }
}
