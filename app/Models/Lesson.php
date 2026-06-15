<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasCatalogScopes;
use Database\Factories\LessonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

final class Lesson extends Model implements HasMedia
{
    /** @use HasFactory<LessonFactory> */
    use HasCatalogScopes, HasFactory, InteractsWithMedia;

    /** @var list<string> */
    protected array $searchable = ['title', 'slug', 'summary', 'content'];

    /** @var list<string> */
    protected $fillable = [
        'course_section_id',
        'title',
        'slug',
        'summary',
        'content',
        'video_url',
        'sort_order',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /** @return BelongsTo<CourseSection, $this> */
    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('lesson-video')
            ->singleFile()
            ->useDisk((string) env('MEDIA_DISK_PRIVATE', 'media_private'));
    }
}
