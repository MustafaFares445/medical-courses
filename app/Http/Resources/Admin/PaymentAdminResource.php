<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Payment
 */
final class PaymentAdminResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'orderId' => $this->order_id,
            'provider' => $this->provider,
            'providerPaymentId' => $this->provider_payment_id,
            'providerSessionId' => $this->provider_session_id,
            'providerEventId' => $this->provider_event_id,
            'status' => $this->status,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'order' => OrderSummaryResource::make($this->whenLoaded('order')),
            'processedAt' => $this->processed_at?->toISOString(),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
