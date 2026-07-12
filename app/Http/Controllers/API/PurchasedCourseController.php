<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProtectedLessonResource;
use App\Http\Resources\PurchasedCourseResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Services\LessonAccessService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class PurchasedCourseController extends Controller
{
    public function __construct(private readonly LessonAccessService $lessons) {}

    public function show(Request $request, Course $course): PurchasedCourseResource
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($this->lessons->hasCourseAccess($user, $course), Response::HTTP_FORBIDDEN);

        $course->load([
            'category',
            'sections' => fn ($query) => $query->orderBy('sort_order'),
            'sections.lessons' => fn ($query) => $query
                ->where('status', 'published')
                ->orderBy('sort_order'),
        ]);

        return PurchasedCourseResource::make($course);
    }

    public function lesson(Request $request, Course $course, Lesson $lesson): ProtectedLessonResource
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($this->lessons->lessonBelongsToCourse($course, $lesson), Response::HTTP_NOT_FOUND);
        abort_unless($lesson->status === 'published', Response::HTTP_NOT_FOUND);
        abort_unless($this->lessons->hasCourseAccess($user, $course), Response::HTTP_FORBIDDEN);

        $lesson->loadMissing('section.course');

        return ProtectedLessonResource::make($lesson);
    }
}
