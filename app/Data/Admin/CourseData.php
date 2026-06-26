<?php

declare(strict_types=1);

namespace App\Data\Admin;

use App\Models\Course;
use App\Support\Locale;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

final class CourseData extends Data
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

    public static function fromValidated(array $validated): self
    {
        return new self(
            categoryId: array_key_exists('categoryId', $validated) && $validated['categoryId'] !== null ? (int) $validated['categoryId'] : null,
            title: is_array($validated['title'] ?? null) ? $validated['title'] : null,
            slug: is_string($validated['slug'] ?? null) ? $validated['slug'] : null,
            shortDescription: array_key_exists('shortDescription', $validated) && is_array($validated['shortDescription']) ? $validated['shortDescription'] : null,
            description: array_key_exists('description', $validated) && is_array($validated['description']) ? $validated['description'] : null,
            price: array_key_exists('price', $validated) ? (string) $validated['price'] : null,
            currency: is_string($validated['currency'] ?? null) ? strtoupper($validated['currency']) : null,
            status: is_string($validated['status'] ?? null) ? $validated['status'] : null,
            fields: array_keys($validated),
        );
    }

    public function toModelAttributes(?Course $course = null): array
    {
        $attributes = [];
        if ($this->hasField('categoryId')) { $attributes['category_id'] = $this->categoryId; }
        if ($this->hasField('title')) { $attributes['title'] = $this->title; }
        if ($this->hasField('slug')) { $attributes['slug'] = $this->slug !== null && $this->slug !== '' ? Str::slug($this->slug) : Str::slug(Locale::slugSource($this->title)); }
        elseif (! $course instanceof Course && $this->title !== null) { $attributes['slug'] = Str::slug(Locale::slugSource($this->title)); }
        if ($this->hasField('shortDescription')) { $attributes['short_description'] = $this->shortDescription; }
        if ($this->hasField('description')) { $attributes['description'] = $this->description; }
        if ($this->hasField('price')) { $attributes['price'] = $this->price; }
        if ($this->hasField('currency')) { $attributes['currency'] = $this->currency; }
        if ($this->hasField('status')) { $attributes['status'] = $this->status; }
        return $attributes;
    }

    private function hasField(string $field): bool
    {
        return in_array($field, $this->fields, true);
    }
}
