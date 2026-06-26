<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
final class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'category_id' => Category::factory()->course(),
            'title' => ['en' => $title, 'ar' => 'كورس '.$title],
            'slug' => Str::slug($title),
            'short_description' => [
                'en' => fake()->sentence(12),
                'ar' => fake()->sentence(12),
            ],
            'description' => [
                'en' => fake()->paragraphs(3, true),
                'ar' => fake()->paragraphs(3, true),
            ],
            'price' => fake()->randomFloat(2, 10, 200),
            'currency' => 'USD',
            'status' => 'draft',
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'hidden']);
    }
}
