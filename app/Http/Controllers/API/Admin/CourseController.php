<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Data\Admin\CourseData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseFilterRequest;
use App\Http\Requests\Admin\CourseRequest;
use App\Http\Resources\Admin\CourseAdminResource;
use App\Models\Course;
use App\Services\Admin\CourseService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class CourseController extends Controller
{
    public function index(CourseFilterRequest $request): AnonymousResourceCollection
    {
        $query = Course::query()
            ->with('category')
            ->withCount(['sections', 'lessons', 'accesses'])
            ->search($request->search());

        if ($request->status() !== null) {
            $query->where('status', $request->status());
        }

        if ($request->categoryId() !== null) {
            $query->where('category_id', $request->categoryId());
        }

        if ($request->priceMin() !== null) {
            $query->where('price', '>=', $request->priceMin());
        }

        if ($request->priceMax() !== null) {
            $query->where('price', '<=', $request->priceMax());
        }

        if ($request->createdAfter() !== null) {
            $query->whereDate('created_at', '>=', $request->createdAfter());
        }

        if ($request->createdBefore() !== null) {
            $query->whereDate('created_at', '<=', $request->createdBefore());
        }

        return CourseAdminResource::collection(
            $query->orderBy($request->sortColumnName(), $request->sortDirectionName())
                ->paginate($request->perPage())
        );
    }

    public function store(CourseRequest $request, CourseService $service): JsonResponse
    {
        $course = $service->create(
            CourseData::fromValidated($request->validated()),
            $request->file('thumbnail')
        );

        return CourseAdminResource::make($this->loadCourse($course))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Course $course): CourseAdminResource
    {
        return CourseAdminResource::make($this->loadCourse($course, true));
    }

    public function update(CourseRequest $request, Course $course, CourseService $service): CourseAdminResource
    {
        $course = $service->update(
            $course,
            CourseData::fromValidated($request->validated()),
            $request->file('thumbnail')
        );

        return CourseAdminResource::make($this->loadCourse($course));
    }

    public function destroy(Course $course, CourseService $service): JsonResponse
    {
        $service->delete($course);

        return ApiResponse::noContent();
    }

    private function loadCourse(Course $course, bool $withStructure = false): Course
    {
        $relations = ['category'];

        if ($withStructure) {
            $relations['sections'] = fn ($query) => $query->orderBy('sort_order');
            $relations['sections.lessons'] = fn ($query) => $query->orderBy('sort_order');
        }

        return $course->load($relations)->loadCount(['sections', 'lessons', 'accesses']);
    }
}
