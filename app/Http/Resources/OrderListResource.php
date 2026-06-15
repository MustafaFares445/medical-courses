<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class OrderListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'orderNumber' => $this->order_number,
            'status' => $this->status,
            'itemsCount' => $this->items_count ?? $this->items()->count(),
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'currency' => $this->currency,
            'paidAt' => $this->paid_at?->toISOString(),
            'createdAt' => $this->created_at?->toISOString(),
        ];
    }
}
