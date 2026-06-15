<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProtectedLessonResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Services\LessonAccessService;
use Illuminate\Http\Request;

final class ProtectedLessonController extends Controller
{
    public function __construct(private readonly LessonAccessService $lessons) {}

    public function show(Request $request, Course $course, Lesson $lesson): ProtectedLessonResource
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($this->lessons->lessonBelongsToCourse($course, $lesson), 404);
        abort_unless($lesson->status === 'published', 404);
        abort_unless($this->lessons->hasCourseAccess($user, $course), 403);

        $lesson->load('section');

        return ProtectedLessonResource::make($lesson);
    }
}
