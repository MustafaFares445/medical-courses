<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Support\Locale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ProtectedLessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [];
    }
}
