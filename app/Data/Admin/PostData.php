<?php

declare(strict_types=1);

namespace App\Data\Admin;

use Spatie\LaravelData\Data;

final class PostData extends Data
{
    /** @param list<string> $fields */
    public function __construct(
        public readonly ?int $categoryId = null,
        public readonly ?string $title = null,
        public readonly ?string $slug = null,
        public readonly ?string $excerpt = null,
        public readonly ?string $body = null,
        public readonly ?string $status = null,
        public readonly array $fields = [],
    ) {}
}
