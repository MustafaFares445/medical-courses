<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseSection>
 */
final class CourseSectionFactory extends Factory
{
    protected $model = CourseSection::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'course_id' => Course::factory(),
            'title' => ['en' => $title, 'ar' => 'قسم '.$title],
            'description' => [
                'en' => fake()->optional()->sentence(10),
                'ar' => fake()->optional()->sentence(10),
            ],
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
