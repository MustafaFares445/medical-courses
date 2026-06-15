<?php

declare(strict_types=1);

namespace App\Data\Auth;

use Spatie\LaravelData\Data;

final class ProfileData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
    ) {}
}
