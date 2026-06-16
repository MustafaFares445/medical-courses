<?php

declare(strict_types=1);

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('denies guests from admin operational views', function (): void {
    $this->getJson('/api/admin/users')->assertUnauthorized();
    $this->getJson('/api/admin/orders')->assertUnauthorized();
    $this->getJson('/api/admin/payments')->assertUnauthorized();
});

it('denies students from admin operational views', function (): void {
    Sanctum::actingAs(User::factory()->student()->create());

    $this->getJson('/api/admin/users')->assertForbidden();
    $this->getJson('/api/admin/orders')->assertForbidden();
    $this->getJson('/api/admin/payments')->assertForbidden();
});

it('lists and shows users with purchase counts', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $student = User::factory()->student()->create([
        'name' => 'Nada Student',
        'email' => 'nada@example.com',
    ]);
    Order::factory()->for($student)->paid()->create();

    $this->getJson('/api/admin/users?filter[userType]=student&search=nada')
        ->assertOk()
        ->assertJsonPath('data.0.email', 'nada@example.com')
        ->assertJsonStructure([
            'data' => [[
                'id',
                'name',
                'email',
                'userType',
                'ordersCount',
                'purchasedCoursesCount',
                'purchasedBooksCount',
                'createdAt',
                'updatedAt',
            ]],
            'links',
            'meta',
        ]);

    $this->getJson("/api/admin/users/{$student->id}")
        ->assertOk()
        ->assertJsonPath('data.email', 'nada@example.com')
        ->assertJsonStructure(['data' => ['orders']]);
});

it('lists and shows orders with items and payments', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $student = User::factory()->student()->create();
    $order = Order::factory()->for($student)->paid()->create([
        'order_number' => 'ORD-TEST-000001',
        'total' => '120.00',
    ]);
    OrderItem::factory()->for($order)->create();
    Payment::factory()->for($order)->paid()->create(['status' => 'paid', 'amount' => '120.00']);
    Order::factory()->create(['status' => Order::STATUS_FAILED, 'order_number' => 'ORD-TEST-FAILED']);

    $this->getJson('/api/admin/orders?filter[status]=paid&search=000001')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.orderNumber', 'ORD-TEST-000001')
        ->assertJsonPath('data.0.status', 'paid');

    $this->getJson("/api/admin/orders/{$order->id}")
        ->assertOk()
        ->assertJsonPath('data.orderNumber', 'ORD-TEST-000001')
        ->assertJsonStructure(['data' => ['customer', 'items', 'payments']]);
});

it('lists and shows payments without raw payloads', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $order = Order::factory()->paid()->create();
    $payment = Payment::factory()->for($order)->paid()->create([
        'provider' => 'stripe',
        'provider_payment_id' => 'pi_test_admin',
        'status' => 'paid',
        'raw_payload' => ['secret' => 'do-not-expose'],
    ]);

    $this->getJson('/api/admin/payments?filter[provider]=stripe&filter[status]=paid&search=pi_test_admin')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.providerPaymentId', 'pi_test_admin')
        ->assertJsonMissing(['rawPayload' => ['secret' => 'do-not-expose']])
        ->assertJsonMissing(['raw_payload' => ['secret' => 'do-not-expose']]);

    $this->getJson("/api/admin/payments/{$payment->id}")
        ->assertOk()
        ->assertJsonPath('data.providerPaymentId', 'pi_test_admin')
        ->assertJsonMissing(['rawPayload' => ['secret' => 'do-not-expose']])
        ->assertJsonMissing(['raw_payload' => ['secret' => 'do-not-expose']]);
});
