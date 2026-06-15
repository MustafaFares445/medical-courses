<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CourseSection;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Lesson>
 */
final class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'course_section_id' => CourseSection::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => fake()->sentence(12),
            'content' => fake()->paragraphs(2, true),
            'video_url' => fake()->optional()->url(),
            'sort_order' => fake()->numberBetween(0, 100),
            'status' => 'draft',
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'published']);
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'hidden']);
    }
}
