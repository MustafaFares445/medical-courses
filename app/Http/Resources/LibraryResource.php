<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class LibraryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'courses' => PurchasedCourseResource::collection($this->resource['courses']),
            'books' => LibraryBookResource::collection($this->resource['books']),
        ];
    }
}
