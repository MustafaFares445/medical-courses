<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
final class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        $title = Str::title($name);

        return [
            'type' => 'course',
            'name' => ['en' => $title, 'ar' => 'تصنيف '.$title],
            'slug' => Str::slug($name),
            'description' => [
                'en' => fake()->optional()->sentence(),
                'ar' => fake()->optional()->sentence(),
            ],
            'is_active' => true,
        ];
    }

    public function course(): static
    {
        return $this->state(fn (array $attributes) => ['type' => 'course']);
    }

    public function book(): static
    {
        return $this->state(fn (array $attributes) => ['type' => 'book']);
    }

    public function article(): static
    {
        return $this->state(fn (array $attributes) => ['type' => 'article']);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
