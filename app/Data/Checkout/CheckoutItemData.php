<?php

declare(strict_types=1);

namespace App\Data\Checkout;

use Spatie\LaravelData\Data;

final class CheckoutItemData extends Data
{
    public function __construct(
        public readonly string $type,
        public readonly int $id,
    ) {}
}
