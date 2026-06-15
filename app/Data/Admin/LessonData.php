<?php

declare(strict_types=1);

namespace App\Data\Admin;

use App\Models\Lesson;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

final class LessonData extends Data
{
    /** @param list<string> $fields */
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $slug = null,
        public readonly ?string $summary = null,
        public readonly ?string $content = null,
        public readonly ?string $videoUrl = null,
        public readonly ?int $sortOrder = null,
        public readonly ?string $status = null,
        public readonly array $fields = [],
    ) {}

    /** @param array<string, mixed> $validated */
    public static function fromValidated(array $validated): self
    {
        return new self(
            title: is_string($validated['title'] ?? null) ? $validated['title'] : null,
            slug: is_string($validated['slug'] ?? null) ? $validated['slug'] : null,
            summary: array_key_exists('summary', $validated) ? (is_string($validated['summary']) ? $validated['summary'] : null) : null,
            content: array_key_exists('content', $validated) ? (is_string($validated['content']) ? $validated['content'] : null) : null,
            videoUrl: array_key_exists('videoUrl', $validated) ? (is_string($validated['videoUrl']) ? $validated['videoUrl'] : null) : null,
            sortOrder: array_key_exists('sortOrder', $validated) ? (int) $validated['sortOrder'] : null,
            status: is_string($validated['status'] ?? null) ? $validated['status'] : null,
            fields: array_keys($validated),
        );
    }

    /** @return array<string, mixed> */
    public function toModelAttributes(?Lesson $lesson = null): array
    {
        $attributes = [];

        if ($this->hasField('title')) {
            $attributes['title'] = $this->title;
        }

        if ($this->hasField('slug')) {
            $attributes['slug'] = $this->slug !== null && $this->slug !== '' ? Str::slug($this->slug) : Str::slug((string) $this->title);
        } elseif (! $lesson instanceof Lesson && $this->title !== null) {
            $attributes['slug'] = Str::slug($this->title);
        }

        if ($this->hasField('summary')) {
            $attributes['summary'] = $this->summary;
        }

        if ($this->hasField('content')) {
            $attributes['content'] = $this->content;
        }

        if ($this->hasField('videoUrl')) {
            $attributes['video_url'] = $this->videoUrl;
        }

        if ($this->hasField('sortOrder')) {
            $attributes['sort_order'] = $this->sortOrder;
        }

        if ($this->hasField('status')) {
            $attributes['status'] = $this->status;
        }

        return $attributes;
    }

    private function hasField(string $field): bool
    {
        return in_array($field, $this->fields, true);
    }
}
