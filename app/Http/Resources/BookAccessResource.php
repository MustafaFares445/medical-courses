<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class BookAccessResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'bookId' => $this->resource['bookId'],
            'title' => $this->resource['title'],
            'accessType' => $this->resource['accessType'],
            'accessUrl' => $this->resource['accessUrl'],
            'expiresAt' => $this->resource['expiresAt'],
        ];
    }
}
