<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Data\Admin\CourseSectionData;
use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

final class CourseSectionService
{
    public function create(Course $course, CourseSectionData $data): CourseSection
    {
        return DB::transaction(function () use ($course, $data): CourseSection {
            /** @var CourseSection $section */
            $section = $course->sections()->create($data->toModelAttributes());

            return $section->refresh();
        });
    }

    public function update(CourseSection $section, CourseSectionData $data): CourseSection
    {
        return DB::transaction(function () use ($section, $data): CourseSection {
            $section->forceFill($data->toModelAttributes())->save();

            return $section->refresh();
        });
    }

    public function delete(CourseSection $section): void
    {
        if ($section->lessons()->exists()) {
            throw new ConflictHttpException('Course section cannot be deleted while it has lessons.');
        }

        DB::transaction(fn (): ?bool => $section->delete());
    }
}
