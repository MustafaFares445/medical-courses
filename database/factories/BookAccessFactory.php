<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Book;
use App\Models\BookAccess;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookAccess>
 */
final class BookAccessFactory extends Factory
{
    protected $model = BookAccess::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory()->published(),
            'order_item_id' => null,
            'purchased_at' => now(),
        ];
    }
}
