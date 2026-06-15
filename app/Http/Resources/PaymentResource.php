<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'providerName' => $this->provider,
            'status' => $this->status,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'processedAt' => $this->processed_at?->toISOString(),
            'createdAt' => $this->created_at?->toISOString(),
        ];
    }
}
