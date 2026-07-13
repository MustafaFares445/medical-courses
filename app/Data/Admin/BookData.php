<?php

declare(strict_types=1);

namespace App\Data\Admin;

use Spatie\LaravelData\Data;

final class BookData extends Data
{
    public function __construct(
        public readonly ?int $categoryId = null,
        public readonly ?array $title = null,
        public readonly ?string $slug = null,
        public readonly ?array $shortDescription = null,
        public readonly ?array $description = null,
        public readonly ?string $price = null,
        public readonly ?string $currency = null,
        public readonly ?string $status = null,
        public readonly array $fields = [],
    ) {}
}
