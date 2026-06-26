<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CategoryAdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->translations('name'),
            'slug' => $this->slug,
            'description' => $this->translations('description'),
            'isActive' => $this->is_active,
            'coursesCount' => $this->whenCounted('courses'),
            'booksCount' => $this->whenCounted('books'),
            'articlesCount' => $this->whenCounted('articles'),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
