<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\BookAccess;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\Order;
use App\Models\User;
use App\Services\StripeCheckoutService;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    app()->instance(StripeCheckoutService::class, new class extends StripeCheckoutService
    {
        public function createSession(Order $order, array $lineItems, string $successUrl, string $cancelUrl): array
        {
            return [
                'id' => 'cs_test_'.$order->id,
                'url' => 'https://checkout.stripe.test/session/'.$order->id,
            ];
        }
    });
});

it('creates a pending checkout order for course and book items using database prices', function (): void {
    $user = User::factory()->student()->create();
    $course = Course::factory()->published()->create(['title' => 'Emergency Course', 'price' => 100, 'currency' => 'USD']);
    $book = Book::factory()->published()->create(['title' => 'Anatomy Book', 'price' => 50, 'currency' => 'USD']);

    Sanctum::actingAs($user);

    $this->postJson('/api/checkout', [
        'items' => [
            ['type' => 'course', 'id' => $course->id, 'price' => 1],
            ['type' => 'book', 'id' => $book->id, 'price' => 1],
        ],
        'successUrl' => 'https://example.test/payment/success',
        'cancelUrl' => 'https://example.test/payment/cancel',
    ])
        ->assertOk()
        ->assertJsonPath('data.checkoutSessionId', 'cs_test_1')
        ->assertJsonPath('data.order.status', 'pending')
        ->assertJsonPath('data.order.total', '150.00')
        ->assertJsonPath('data.order.items.0.title', 'Emergency Course')
        ->assertJsonPath('data.order.items.1.title', 'Anatomy Book');

    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'status' => 'pending',
        'total' => 150,
    ]);

    $this->assertDatabaseHas('payments', [
        'provider_session_id' => 'cs_test_1',
        'status' => 'pending',
    ]);
});

it('rejects unpublished checkout items', function (): void {
    $user = User::factory()->student()->create();
    $course = Course::factory()->hidden()->create();

    Sanctum::actingAs($user);

    $this->postJson('/api/checkout', [
        'items' => [['type' => 'course', 'id' => $course->id]],
        'successUrl' => 'https://example.test/payment/success',
        'cancelUrl' => 'https://example.test/payment/cancel',
    ])->assertUnprocessable();
});

it('rejects items already owned by the user', function (): void {
    $user = User::factory()->student()->create();
    $course = Course::factory()->published()->create();
    $book = Book::factory()->published()->create();

    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);
    BookAccess::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);

    Sanctum::actingAs($user);

    $this->postJson('/api/checkout', [
        'items' => [['type' => 'course', 'id' => $course->id]],
        'successUrl' => 'https://example.test/payment/success',
        'cancelUrl' => 'https://example.test/payment/cancel',
    ])->assertUnprocessable();
});

it('requires authentication to create checkout', function (): void {
    $course = Course::factory()->published()->create();

    $this->postJson('/api/checkout', [
        'items' => [['type' => 'course', 'id' => $course->id]],
        'successUrl' => 'https://example.test/payment/success',
        'cancelUrl' => 'https://example.test/payment/cancel',
    ])->assertUnauthorized();
});
