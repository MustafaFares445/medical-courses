<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CheckoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order' => OrderDetailResource::make($this->resource['order']),
            'checkoutSessionId' => $this->resource['checkoutSessionId'],
            'checkoutUrl' => $this->resource['checkoutUrl'],
        ];
    }
}
