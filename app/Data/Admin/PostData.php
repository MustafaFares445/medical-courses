<?php

declare(strict_types=1);

namespace App\Data\Admin;

use Spatie\LaravelData\Data;

final class PostData extends Data
{
    public function __construct(
        public readonly ?int $categoryId = null,
        public readonly ?array $title = null,
        public readonly ?string $slug = null,
        public readonly ?array $excerpt = null,
        public readonly ?array $body = null,
        public readonly ?string $status = null,
        public readonly array $fields = [],
    ) {}
}
