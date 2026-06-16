<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Order
 */
final class OrderAdminResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'orderNumber' => $this->order_number,
            'status' => $this->status,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'currency' => $this->currency,
            'checkoutSessionId' => $this->checkout_session_id,
            'paidAt' => $this->paid_at?->toISOString(),
            'customer' => UserSummaryResource::make($this->whenLoaded('user')),
            'items' => PurchaseLineResource::collection($this->whenLoaded('items')),
            'payments' => PaymentSummaryResource::collection($this->whenLoaded('payments')),
            'itemsCount' => $this->whenCounted('items'),
            'paymentsCount' => $this->whenCounted('payments'),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
