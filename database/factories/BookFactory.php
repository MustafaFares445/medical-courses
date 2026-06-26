<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Book>
 */
final class BookFactory extends Factory
{
    protected $model = Book::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'category_id' => Category::factory()->book(),
            'title' => ['en' => $title, 'ar' => 'كتاب '.$title],
            'slug' => Str::slug($title),
            'short_description' => [
                'en' => fake()->sentence(12),
                'ar' => fake()->sentence(12),
            ],
            'description' => [
                'en' => fake()->paragraphs(3, true),
                'ar' => fake()->paragraphs(3, true),
            ],
            'price' => fake()->randomFloat(2, 5, 150),
            'currency' => 'USD',
            'external_file_url' => null,
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
