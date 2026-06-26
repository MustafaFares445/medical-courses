<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Support\Locale;

trait HasTranslatableContent
{
    /** @param mixed $value */
    public function translate(mixed $value, string $locale): ?string
    {
        return Locale::translate($value, $locale);
    }

    public function localized(string $attribute, string $locale): ?string
    {
        return Locale::translate($this->getAttribute($attribute), $locale);
    }

    /** @return array{en: ?string, ar: ?string} */
    public function translations(string $attribute): array
    {
        return Locale::translations($this->getAttribute($attribute));
    }
}
