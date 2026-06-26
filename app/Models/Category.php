<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasCatalogScopes;
use App\Models\Concerns\HasTranslatableContent;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Category extends Model
{
    use HasCatalogScopes, HasFactory, HasTranslatableContent;

    protected array $searchable = ['name', 'slug', 'description'];

    protected array $translatable = ['name', 'description'];

    protected $fillable = [
        'type',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
