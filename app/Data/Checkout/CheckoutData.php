<?php

declare(strict_types=1);

namespace App\Data\Checkout;

use Spatie\LaravelData\Data;

final class CheckoutData extends Data
{
    public function __construct(
        public readonly array $items,
        public readonly string $successUrl,
        public readonly string $cancelUrl,
    ) {}
}
