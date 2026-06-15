<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\Lesson;
use App\Models\User;

final class LessonAccessService
{
    public function hasCourseAccess(User $user, Course $course): bool
    {
        return CourseAccess::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();
    }

    public function lessonBelongsToCourse(Course $course, Lesson $lesson): bool
    {
        $lesson->loadMissing('section');

        return $lesson->section !== null && (int) $lesson->section->course_id === (int) $course->id;
    }
}
