<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Support\Locale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = Locale::fromRequest($request);

        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->localized('name', $locale),
            'slug' => $this->slug,
            'description' => $this->localized('description', $locale),
            'isActive' => $this->is_active,
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
