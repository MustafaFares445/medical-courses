<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Data\Admin\LessonData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LessonFilterRequest;
use App\Http\Requests\Admin\LessonRequest;
use App\Http\Resources\Admin\LessonAdminResource;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Services\Admin\LessonService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class LessonController extends Controller
{
    public function index(LessonFilterRequest $request, CourseSection $section): AnonymousResourceCollection
    {
        $query = $section->lessons()->search($request->search());

        if ($request->status() !== null) {
            $query->where('status', $request->status());
        }

        if ($request->createdAfter() !== null) {
            $query->whereDate('created_at', '>=', $request->createdAfter());
        }

        if ($request->createdBefore() !== null) {
            $query->whereDate('created_at', '<=', $request->createdBefore());
        }

        return LessonAdminResource::collection(
            $query->orderBy($request->sortColumnName(), $request->sortDirectionName())
                ->paginate($request->perPage())
        );
    }

    public function store(LessonRequest $request, CourseSection $section, LessonService $service): JsonResponse
    {
        $lesson = $service->create(
            $section,
            LessonData::fromValidated($request->validated()),
            $request->file('lessonVideo')
        );

        return LessonAdminResource::make($lesson)
            ->response()
            ->setStatusCode(201);
    }

    public function show(Lesson $lesson): LessonAdminResource
    {
        return LessonAdminResource::make($lesson->load('section'));
    }

    public function update(LessonRequest $request, Lesson $lesson, LessonService $service): LessonAdminResource
    {
        $lesson = $service->update(
            $lesson,
            LessonData::fromValidated($request->validated()),
            $request->file('lessonVideo')
        );

        return LessonAdminResource::make($lesson);
    }

    public function destroy(Lesson $lesson, LessonService $service): JsonResponse
    {
        $service->delete($lesson);

        return ApiResponse::noContent();
    }
}
