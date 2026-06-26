<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Support\Locale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class LessonPublicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = Locale::fromRequest($request);

        return [
            'id' => $this->id,
            'title' => $this->localized('title', $locale),
            'slug' => $this->slug,
            'summary' => $this->localized('summary', $locale),
            'sortOrder' => $this->sort_order,
        ];
    }
}
