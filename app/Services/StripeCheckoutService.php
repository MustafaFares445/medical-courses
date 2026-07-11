<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use Stripe\StripeClient;

class StripeCheckoutService
{
    public function createSession(Order $order, array $lineItems, string $successUrl, string $cancelUrl): array
    {
        $client = new StripeClient((string) config('services.stripe.api'));

        $session = $client->checkout->sessions->create([
            'mode' => 'payment',
            'client_reference_id' => $order->order_number,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'line_items' => $lineItems,
            'metadata' => [
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
            ],
        ]);

        return [
            'id' => $session->id,
            'url' => $session->url,
        ];
    }
}
