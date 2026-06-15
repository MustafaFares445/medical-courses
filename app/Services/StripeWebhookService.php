<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Stripe\Webhook;
use Throwable;

final class StripeWebhookService
{
    public function __construct(private readonly AccessGrantingService $access) {}

    public function eventFromPayload(string $payload, ?string $signature): array
    {
        $webhook = config('services.stripe.webhook');

        if (app()->environment('testing') && empty($webhook)) {
            return json_decode($payload, true, flags: JSON_THROW_ON_ERROR);
        }

        $event = Webhook::constructEvent($payload, (string) $signature, (string) $webhook);

        return $event->toArray();
    }

    public function process(array $event): void
    {
        $eventId = (string) Arr::get($event, 'id');
        $type = (string) Arr::get($event, 'type');
        $session = Arr::get($event, 'data.object', []);
        $sessionId = (string) Arr::get($session, 'id');

        if ($eventId !== '' && Payment::query()->where('provider_event_id', $eventId)->exists()) {
            return;
        }

        if ($sessionId === '') {
            return;
        }

        $order = Order::query()->where('checkout_session_id', $sessionId)->first();

        if (! $order) {
            return;
        }

        if ($type === 'checkout.session.completed') {
            $this->markPaid($order, $event, $session, $eventId, $sessionId);
            return;
        }

        if ($type === 'checkout.session.expired') {
            $order->forceFill(['status' => Order::STATUS_EXPIRED])->save();
            $this->recordPayment($order, $event, $session, $eventId, $sessionId, 'expired');
            return;
        }

        if ($type === 'checkout.session.async_payment_failed') {
            $order->forceFill(['status' => Order::STATUS_FAILED])->save();
            $this->recordPayment($order, $event, $session, $eventId, $sessionId, 'failed');
        }
    }

    private function markPaid(Order $order, array $event, array $session, string $eventId, string $sessionId): void
    {
        if ($order->status !== Order::STATUS_PAID) {
            $order->forceFill([
                'status' => Order::STATUS_PAID,
                'paid_at' => now(),
            ])->save();
        }

        $this->recordPayment($order, $event, $session, $eventId, $sessionId, 'paid');
        $this->access->grant($order);
    }

    private function recordPayment(Order $order, array $event, array $session, string $eventId, string $sessionId, string $status): void
    {
        Payment::query()->updateOrCreate(
            ['provider_session_id' => $sessionId],
            [
                'order_id' => $order->id,
                'provider' => 'stripe',
                'provider_payment_id' => Arr::get($session, 'payment_intent'),
                'provider_event_id' => $eventId !== '' ? $eventId : null,
                'status' => $status,
                'amount' => ((int) Arr::get($session, 'amount_total', (int) round((float) $order->total * 100))) / 100,
                'currency' => strtoupper((string) Arr::get($session, 'currency', $order->currency)),
                'raw_payload' => $event,
                'processed_at' => Carbon::now(),
            ],
        );
    }
}
