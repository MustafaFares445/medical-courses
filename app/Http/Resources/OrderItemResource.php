<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->item_type,
            'itemId' => $this->item_id,
            'title' => $this->title_snapshot,
            'price' => $this->price_snapshot,
            'currency' => $this->currency,
        ];
    }
}
