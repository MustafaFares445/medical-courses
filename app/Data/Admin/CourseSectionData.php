<?php

declare(strict_types=1);

namespace App\Data\Admin;

use Spatie\LaravelData\Data;

final class CourseSectionData extends Data
{
    /** @param list<string> $fields */
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?int $sortOrder = null,
        public readonly array $fields = [],
    ) {}

    /** @param array<string, mixed> $validated */
    public static function fromValidated(array $validated): self
    {
        return new self(
            title: is_string($validated['title'] ?? null) ? $validated['title'] : null,
            description: array_key_exists('description', $validated) ? (is_string($validated['description']) ? $validated['description'] : null) : null,
            sortOrder: array_key_exists('sortOrder', $validated) ? (int) $validated['sortOrder'] : null,
            fields: array_keys($validated),
        );
    }

    /** @return array<string, mixed> */
    public function toModelAttributes(): array
    {
        $attributes = [];

        if ($this->hasField('title')) {
            $attributes['title'] = $this->title;
        }

        if ($this->hasField('description')) {
            $attributes['description'] = $this->description;
        }

        if ($this->hasField('sortOrder')) {
            $attributes['sort_order'] = $this->sortOrder;
        }

        return $attributes;
    }

    private function hasField(string $field): bool
    {
        return in_array($field, $this->fields, true);
    }
}
