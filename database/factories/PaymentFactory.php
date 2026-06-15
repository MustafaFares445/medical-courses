<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
final class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'provider' => 'stripe',
            'provider_payment_id' => 'pi_'.fake()->unique()->lexify('????????????????'),
            'provider_session_id' => 'cs_test_'.fake()->unique()->lexify('????????????????'),
            'provider_event_id' => 'evt_'.fake()->unique()->lexify('????????????????'),
            'status' => 'pending',
            'amount' => fake()->randomFloat(2, 10, 300),
            'currency' => 'USD',
            'raw_payload' => [],
            'processed_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'processed_at' => now(),
        ]);
    }
}
