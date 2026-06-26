<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasCatalogScopes;
use App\Models\Concerns\HasTranslatableContent;
use Database\Factories\LessonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

final class Lesson extends Model implements HasMedia
{
    use HasCatalogScopes, HasFactory, HasTranslatableContent, InteractsWithMedia;

    protected array $searchable = ['title', 'slug', 'summary', 'content'];
    protected array $translatable = ['title', 'summary', 'content'];

    protected $fillable = ['course_section_id', 'title', 'slug', 'summary', 'content', 'video_url', 'sort_order', 'status'];

    protected function casts(): array
    {
        return ['title' => 'array', 'summary' => 'array', 'content' => 'array', 'sort_order' => 'integer'];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('lesson-video')->singleFile()->useDisk((string) env('MEDIA_DISK_PRIVATE', 'media_private'));
    }
}
