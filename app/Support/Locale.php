<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;

final class Locale
{
    public const FALLBACK = 'en';

    /** @return list<string> */
    public static function supported(): array
    {
        return ['en', 'ar'];
    }

    public static function fromRequest(Request $request): string
    {
        $header = strtolower((string) $request->header('Accept-Language', self::FALLBACK));

        foreach (explode(',', $header) as $language) {
            $code = substr(trim(explode(';', $language)[0]), 0, 2);
            if (in_array($code, self::supported(), true)) {
                return $code;
            }
        }

        return self::FALLBACK;
    }

    /** @param mixed $value */
    public static function translate(mixed $value, string $locale): ?string
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded;
            } else {
                return $value;
            }
        }

        if (! is_array($value)) {
            return null;
        }

        $localized = $value[$locale] ?? null;
        if (is_string($localized) && $localized !== '') {
            return $localized;
        }

        $fallback = $value[self::FALLBACK] ?? null;

        return is_string($fallback) && $fallback !== '' ? $fallback : null;
    }

    /** @param mixed $value */
    public static function translations(mixed $value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded;
            } else {
                return ['en' => $value, 'ar' => null];
            }
        }

        if (! is_array($value)) {
            return ['en' => null, 'ar' => null];
        }

        return [
            'en' => isset($value['en']) && is_string($value['en']) ? $value['en'] : null,
            'ar' => isset($value['ar']) && is_string($value['ar']) ? $value['ar'] : null,
        ];
    }

    /** @param mixed $value */
    public static function slugSource(mixed $value): string
    {
        return self::translate($value, self::FALLBACK) ?? self::translate($value, 'ar') ?? '';
    }
}
