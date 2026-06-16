<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasCatalogScopes;
use Database\Factories\CourseSectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class CourseSection extends Model
{
    /** @use HasFactory<CourseSectionFactory> */
    use HasCatalogScopes, HasFactory;

    /** @var list<string> */
    protected array $searchable = ['title', 'description'];

    /** @var list<string> */
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'sort_order',
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

    /** @return BelongsTo<Course, $this> */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** @return HasMany<Lesson, $this> */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }
}
