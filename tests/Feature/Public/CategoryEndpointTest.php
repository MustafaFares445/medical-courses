<?php

declare(strict_types=1);

use App\Models\Category;

it('returns active public categories and filters by type', function (): void {
    Category::factory()->course()->create(['name' => 'Cardiology', 'slug' => 'cardiology']);
    Category::factory()->book()->create(['name' => 'Anatomy', 'slug' => 'anatomy']);
    Category::factory()->course()->inactive()->create(['name' => 'Inactive', 'slug' => 'inactive']);

    $this->getJson('/api/categories?filter[type]=course')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Cardiology')
        ->assertJsonPath('data.0.isActive', true);
});
