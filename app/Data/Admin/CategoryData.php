<?php

declare(strict_types=1);

namespace App\Data\Admin;

use App\Models\Category;
use App\Support\Locale;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

final class CategoryData extends Data
{
    /**
     * @param  array{en?: string|null, ar?: string|null}|null  $name
     * @param  array{en?: string|null, ar?: string|null}|null  $description
     * @param  list<string>  $fields
     */
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?array $name = null,
        public readonly ?string $slug = null,
        public readonly ?array $description = null,
        public readonly ?bool $isActive = null,
        public readonly array $fields = [],
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated): self
    {
        return new self(
            type: is_string($validated['type'] ?? null) ? $validated['type'] : null,
            name: is_array($validated['name'] ?? null) ? $validated['name'] : null,
            slug: is_string($validated['slug'] ?? null) ? $validated['slug'] : null,
            description: array_key_exists('description', $validated) && is_array($validated['description']) ? $validated['description'] : null,
            isActive: array_key_exists('isActive', $validated) ? (bool) $validated['isActive'] : null,
            fields: array_keys($validated),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toModelAttributes(?Category $category = null): array
    {
        $attributes = [];

        if ($this->hasField('type')) {
            $attributes['type'] = $this->type;
        }

        if ($this->hasField('name')) {
            $attributes['name'] = $this->name;
        }

        if ($this->hasField('slug')) {
            $attributes['slug'] = $this->slug !== null && $this->slug !== ''
                ? Str::slug($this->slug)
                : Str::slug(Locale::slugSource($this->name));
        } elseif (! $category instanceof Category && $this->name !== null) {
            $attributes['slug'] = Str::slug(Locale::slugSource($this->name));
        }

        if ($this->hasField('description')) {
            $attributes['description'] = $this->description;
        }

        if ($this->hasField('isActive')) {
            $attributes['is_active'] = $this->isActive;
        }

        return $attributes;
    }

    private function hasField(string $field): bool
    {
        return in_array($field, $this->fields, true);
    }
}
