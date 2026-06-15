<?php

declare(strict_types=1);

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('lists only the authenticated user orders', function (): void {
    $user = User::factory()->student()->create();
    $otherUser = User::factory()->student()->create();

    $ownOrder = Order::factory()->for($user)->paid()->create(['order_number' => 'ORD-OWN-001']);
    OrderItem::factory()->for($ownOrder)->create(['title_snapshot' => 'Own Course']);

    $otherOrder = Order::factory()->for($otherUser)->paid()->create(['order_number' => 'ORD-OTHER-001']);
    OrderItem::factory()->for($otherOrder)->create(['title_snapshot' => 'Other Course']);

    Sanctum::actingAs($user);

    $this->getJson('/api/my/orders')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.orderNumber', 'ORD-OWN-001')
        ->assertJsonPath('data.0.itemsCount', 1)
        ->assertJsonMissing(['orderNumber' => 'ORD-OTHER-001']);
});

it('shows the authenticated user order details with items and payments', function (): void {
    $user = User::factory()->student()->create();
    $order = Order::factory()->for($user)->paid()->create([
        'order_number' => 'ORD-DETAIL-001',
        'total' => 120,
        'subtotal' => 120,
    ]);

    OrderItem::factory()->for($order)->create(['title_snapshot' => 'Clinical Course']);
    Payment::factory()->for($order)->paid()->create(['amount' => 120]);

    Sanctum::actingAs($user);

    $this->getJson('/api/my/orders/ORD-DETAIL-001')
        ->assertOk()
        ->assertJsonPath('data.orderNumber', 'ORD-DETAIL-001')
        ->assertJsonPath('data.items.0.title', 'Clinical Course')
        ->assertJsonPath('data.payments.0.status', 'paid')
        ->assertJsonMissing(['rawPayload' => []]);
});

it('does not expose another user order details', function (): void {
    $user = User::factory()->student()->create();
    $otherUser = User::factory()->student()->create();
    Order::factory()->for($otherUser)->paid()->create(['order_number' => 'ORD-PRIVATE-001']);

    Sanctum::actingAs($user);

    $this->getJson('/api/my/orders/ORD-PRIVATE-001')->assertNotFound();
});

it('requires authentication for order history', function (): void {
    $this->getJson('/api/my/orders')->assertUnauthorized();
});
