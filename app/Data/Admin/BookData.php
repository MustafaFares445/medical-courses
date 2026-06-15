<?php

declare(strict_types=1);

namespace App\Data\Admin;

use Spatie\LaravelData\Data;

final class BookData extends Data
{
    public function __construct(
        public readonly ?int $categoryId = null,
        public readonly ?string $title = null,
        public readonly ?string $slug = null,
        public readonly ?string $shortDescription = null,
        public readonly ?string $description = null,
        public readonly ?string $price = null,
        public readonly ?string $currency = null,
        public readonly ?string $externalFileUrl = null,
        public readonly ?string $status = null,
        public readonly array $fields = [],
    ) {}
}
