<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

it('creates the core application tables', function (): void {
    expect(Schema::hasTable('users'))->toBeTrue()
        ->and(Schema::hasTable('categories'))->toBeTrue()
        ->and(Schema::hasTable('courses'))->toBeTrue()
        ->and(Schema::hasTable('course_sections'))->toBeTrue()
        ->and(Schema::hasTable('lessons'))->toBeTrue()
        ->and(Schema::hasTable('books'))->toBeTrue()
        ->and(Schema::hasTable('articles'))->toBeTrue()
        ->and(Schema::hasTable('orders'))->toBeTrue()
        ->and(Schema::hasTable('order_items'))->toBeTrue()
        ->and(Schema::hasTable('payments'))->toBeTrue()
        ->and(Schema::hasTable('course_accesses'))->toBeTrue()
        ->and(Schema::hasTable('book_accesses'))->toBeTrue()
        ->and(Schema::hasTable('media'))->toBeTrue();
});

it('adds required content columns', function (): void {
    expect(Schema::hasColumns('courses', [
        'category_id',
        'title',
        'slug',
        'short_description',
        'description',
        'price',
        'currency',
        'status',
        'published_at',
        'deleted_at',
    ]))->toBeTrue()
        ->and(Schema::hasColumns('lessons', [
            'course_section_id',
            'title',
            'summary',
            'content',
            'video_url',
            'sort_order',
            'status',
        ]))->toBeTrue()
        ->and(Schema::hasColumns('books', [
            'category_id',
            'title',
            'slug',
            'price',
            'external_file_url',
            'status',
            'deleted_at',
        ]))->toBeTrue();
});

it('does not add version two fields to lessons', function (): void {
    expect(Schema::hasColumn('lessons', 'is_preview'))->toBeFalse();
});
