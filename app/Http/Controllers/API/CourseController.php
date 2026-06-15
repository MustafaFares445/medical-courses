<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseFilterRequest;
use App\Http\Resources\CourseDetailResource;
use App\Http\Resources\CourseListResource;
use App\Models\Course;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class CourseController extends Controller
{
    public function index(CourseFilterRequest $request): AnonymousResourceCollection
    {
        $query = Course::query()
            ->published()
            ->with('category')
            ->search($request->search())
            ->category($request->categoryId());

        if ($request->categorySlug() !== null) {
            $query->whereHas('category', fn ($category) => $category
                ->where('slug', $request->categorySlug())
                ->where('type', 'course'));
        }

        if ($request->priceMin() !== null) {
            $query->where('price', '>=', $request->priceMin());
        }

        if ($request->priceMax() !== null) {
            $query->where('price', '<=', $request->priceMax());
        }

        return CourseListResource::collection(
            $query->orderBy($request->sortColumn(), $request->sortDirection())
                ->paginate($request->perPage())
        );
    }

    public function show(Course $course): CourseDetailResource
    {
        abort_unless($course->status === 'published', 404);

        $course->load([
            'category',
            'sections' => fn ($query) => $query->orderBy('sort_order'),
            'sections.lessons' => fn ($query) => $query
                ->where('status', 'published')
                ->orderBy('sort_order'),
        ]);

        return CourseDetailResource::make($course);
    }
}
