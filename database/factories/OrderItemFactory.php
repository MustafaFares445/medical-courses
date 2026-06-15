<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Book;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
final class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $course = Course::factory()->published()->create();

        return [
            'order_id' => Order::factory(),
            'item_type' => 'course',
            'item_id' => $course->id,
            'title_snapshot' => $course->title,
            'price_snapshot' => $course->price,
            'currency' => $course->currency,
        ];
    }

    public function course(?Course $course = null): static
    {
        return $this->state(function (array $attributes) use ($course): array {
            $course ??= Course::factory()->published()->create();

            return [
                'item_type' => 'course',
                'item_id' => $course->id,
                'title_snapshot' => $course->title,
                'price_snapshot' => $course->price,
                'currency' => $course->currency,
            ];
        });
    }

    public function book(?Book $book = null): static
    {
        return $this->state(function (array $attributes) use ($book): array {
            $book ??= Book::factory()->published()->create();

            return [
                'item_type' => 'book',
                'item_id' => $book->id,
                'title_snapshot' => $book->title,
                'price_snapshot' => $book->price,
                'currency' => $book->currency,
            ];
        });
    }
}
