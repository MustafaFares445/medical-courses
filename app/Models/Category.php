<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasCatalogScopes;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasCatalogScopes, HasFactory;

    /** @var list<string> */
    protected array $searchable = ['name', 'slug', 'description'];

    /** @var list<string> */
    protected $fillable = [
        'type',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** @return HasMany<Course, $this> */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /** @return HasMany<Book, $this> */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /** @return HasMany<Article, $this> */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
