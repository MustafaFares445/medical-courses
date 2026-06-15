<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class CategorySeeder extends Seeder
{
    public function run(): void
    {
        collect([
            ['type' => 'course', 'name' => 'Emergency Medicine'],
            ['type' => 'course', 'name' => 'Cardiology'],
            ['type' => 'course', 'name' => 'Internal Medicine'],
            ['type' => 'book', 'name' => 'Clinical Guides'],
            ['type' => 'book', 'name' => 'Anatomy'],
            ['type' => 'article', 'name' => 'Study Tips'],
            ['type' => 'article', 'name' => 'Medical Education'],
        ])->each(function (array $category): void {
            Category::query()->updateOrCreate(
                [
                    'type' => $category['type'],
                    'slug' => Str::slug($category['name']),
                ],
                [
                    'name' => $category['name'],
                    'description' => null,
                    'is_active' => true,
                ],
            );
        });
    }
}
