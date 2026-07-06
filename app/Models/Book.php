<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasCatalogScopes;
use App\Models\Concerns\HasTranslatableContent;
use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class Book extends Model implements HasMedia
{
    use HasCatalogScopes, HasFactory, HasTranslatableContent, InteractsWithMedia, SoftDeletes;

    protected array $searchable = ['title', 'slug', 'short_description', 'description'];

    protected array $translatable = ['title', 'short_description', 'description'];

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'short_description',
        'description',
        'price',
        'currency',
        'external_file_url',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'short_description' => 'array',
            'description' => 'array',
            'price' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function accesses(): HasMany
    {
        return $this->hasMany(BookAccess::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->singleFile()
            ->useDisk((string) env('MEDIA_DISK_PUBLIC', 'media_public'));

        $this->addMediaCollection('book-file')
            ->singleFile()
            ->useDisk((string) env('MEDIA_DISK_PRIVATE', 'local'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('cover-card')
            ->performOnCollections('cover')
            ->width(480)
            ->height(640)
            ->nonQueued();
    }
}
