<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PurchasedCourseResource;
use App\Models\Course;
use App\Models\User;
use App\Services\LessonAccessService;
use Illuminate\Http\Request;

final class PurchasedCourseController extends Controller
{
    public function __construct(private readonly LessonAccessService $lessons) {}

    public function show(Request $request, Course $course): PurchasedCourseResource
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($this->lessons->hasCourseAccess($user, $course), 403);

        $course->load([
            'category',
            'sections' => fn ($query) => $query->orderBy('sort_order'),
            'sections.lessons' => fn ($query) => $query
                ->where('status', 'published')
                ->orderBy('sort_order'),
        ]);

        return PurchasedCourseResource::make($course);
    }
}
