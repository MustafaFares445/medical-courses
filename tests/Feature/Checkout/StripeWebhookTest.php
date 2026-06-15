<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\BookAccess;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\Config;

function stripeCheckoutEvent(string $eventId, string $type, string $sessionId, int $amount = 10000): array
{
    return [
        'id' => $eventId,
        'type' => $type,
        'data' => [
            'object' => [
                'id' => $sessionId,
                'payment_intent' => 'pi_test_'.$eventId,
                'amount_total' => $amount,
                'currency' => 'usd',
            ],
        ],
    ];
}

it('marks checkout orders paid and grants purchased access from webhook', function (): void {
    $course = Course::factory()->published()->create();
    $book = Book::factory()->published()->create();
    $order = Order::factory()->create([
        'checkout_session_id' => 'cs_test_paid',
        'status' => Order::STATUS_PENDING,
        'total' => 150,
        'currency' => 'USD',
    ]);

    $courseItem = OrderItem::factory()->for($order)->course($course)->create();
    $bookItem = OrderItem::factory()->for($order)->book($book)->create();
    Payment::factory()->for($order)->create(['provider_session_id' => 'cs_test_paid']);

    $this->postJson('/api/stripe/webhook', stripeCheckoutEvent(
        eventId: 'evt_paid_001',
        type: 'checkout.session.completed',
        sessionId: 'cs_test_paid',
        amount: 15000,
    ))->assertOk()->assertJsonPath('data.received', true);

    $order->refresh();

    expect($order->status)->toBe(Order::STATUS_PAID);
    expect($order->paid_at)->not->toBeNull();

    $this->assertDatabaseHas('payments', [
        'provider_session_id' => 'cs_test_paid',
        'provider_event_id' => 'evt_paid_001',
        'status' => 'paid',
    ]);
    $this->assertDatabaseHas('course_accesses', [
        'user_id' => $order->user_id,
        'course_id' => $course->id,
        'order_item_id' => $courseItem->id,
    ]);
    $this->assertDatabaseHas('book_accesses', [
        'user_id' => $order->user_id,
        'book_id' => $book->id,
        'order_item_id' => $bookItem->id,
    ]);
});

it('processes duplicate webhook events idempotently', function (): void {
    $course = Course::factory()->published()->create();
    $order = Order::factory()->create(['checkout_session_id' => 'cs_test_duplicate']);
    OrderItem::factory()->for($order)->course($course)->create();

    $event = stripeCheckoutEvent('evt_duplicate_001', 'checkout.session.completed', 'cs_test_duplicate');

    $this->postJson('/api/stripe/webhook', $event)->assertOk();
    $this->postJson('/api/stripe/webhook', $event)->assertOk();

    expect(CourseAccess::query()->where('user_id', $order->user_id)->where('course_id', $course->id)->count())->toBe(1);
    expect(Payment::query()->where('provider_event_id', 'evt_duplicate_001')->count())->toBe(1);
});

it('marks checkout orders expired from webhook', function (): void {
    $order = Order::factory()->create([
        'checkout_session_id' => 'cs_test_expired',
        'status' => Order::STATUS_PENDING,
    ]);

    $this->postJson('/api/stripe/webhook', stripeCheckoutEvent(
        eventId: 'evt_expired_001',
        type: 'checkout.session.expired',
        sessionId: 'cs_test_expired',
    ))->assertOk();

    expect($order->fresh()->status)->toBe(Order::STATUS_EXPIRED);
});

it('accepts missing orders without creating payments or access records', function (): void {
    $this->postJson('/api/stripe/webhook', stripeCheckoutEvent(
        eventId: 'evt_missing_001',
        type: 'checkout.session.completed',
        sessionId: 'cs_missing',
    ))->assertOk();

    expect(Payment::query()->count())->toBe(0);
    expect(CourseAccess::query()->count())->toBe(0);
    expect(BookAccess::query()->count())->toBe(0);
});

it('rejects invalid webhook payloads', function (): void {
    $this->call('POST', '/api/stripe/webhook', [], [], [], ['CONTENT_TYPE' => 'application/json'], '{bad-json')
        ->assertBadRequest();
});

it('rejects invalid webhook signatures when verification is enabled', function (): void {
    Config::set('services.stripe.webhook', 'whsec_test');

    $this->postJson('/api/stripe/webhook', stripeCheckoutEvent(
        eventId: 'evt_bad_signature',
        type: 'checkout.session.completed',
        sessionId: 'cs_bad_signature',
    ), ['Stripe-Signature' => 'invalid'])
        ->assertBadRequest();
});
