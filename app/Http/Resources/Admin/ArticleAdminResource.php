<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ArticleAdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'categoryId' => $this->category_id,
            'title' => $this->translations('title'),
            'slug' => $this->slug,
            'excerpt' => $this->translations('excerpt'),
            'body' => $this->translations('body'),
            'status' => $this->status,
            'publishedAt' => $this->published_at?->toISOString(),
            'articleImage' => $this->getFirstMediaUrl('article-image') ?: null,
            'category' => CategoryAdminResource::make($this->whenLoaded('category')),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
