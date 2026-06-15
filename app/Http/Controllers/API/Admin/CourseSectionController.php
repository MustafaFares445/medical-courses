<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Data\Admin\CourseSectionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseSectionFilterRequest;
use App\Http\Requests\Admin\CourseSectionRequest;
use App\Http\Resources\Admin\CourseSectionAdminResource;
use App\Models\Course;
use App\Models\CourseSection;
use App\Services\Admin\CourseSectionService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class CourseSectionController extends Controller
{
    public function index(CourseSectionFilterRequest $request, Course $course): AnonymousResourceCollection
    {
        $query = $course->sections()
            ->withCount('lessons')
            ->search($request->search());

        if ($request->createdAfter() !== null) {
            $query->whereDate('created_at', '>=', $request->createdAfter());
        }

        if ($request->createdBefore() !== null) {
            $query->whereDate('created_at', '<=', $request->createdBefore());
        }

        return CourseSectionAdminResource::collection(
            $query->orderBy($request->sortColumnName(), $request->sortDirectionName())
                ->paginate($request->perPage())
        );
    }

    public function store(CourseSectionRequest $request, Course $course, CourseSectionService $service): JsonResponse
    {
        $section = $service->create($course, CourseSectionData::fromValidated($request->validated()));

        return CourseSectionAdminResource::make($section->loadCount('lessons'))
            ->response()
            ->setStatusCode(201);
    }

    public function show(CourseSection $section): CourseSectionAdminResource
    {
        return CourseSectionAdminResource::make(
            $section->load(['lessons' => fn ($query) => $query->orderBy('sort_order')])->loadCount('lessons')
        );
    }

    public function update(CourseSectionRequest $request, CourseSection $section, CourseSectionService $service): CourseSectionAdminResource
    {
        $section = $service->update($section, CourseSectionData::fromValidated($request->validated()));

        return CourseSectionAdminResource::make($section->loadCount('lessons'));
    }

    public function destroy(CourseSection $section, CourseSectionService $service): JsonResponse
    {
        $service->delete($section);

        return ApiResponse::noContent();
    }
}
