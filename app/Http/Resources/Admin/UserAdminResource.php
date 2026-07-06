<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserAdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'userType' => $this->user_type,
            'isActive' => $this->is_active,
            'ordersCount' => $this->whenCounted('orders'),
            'purchasedCoursesCount' => $this->whenCounted('courseAccesses'),
            'purchasedBooksCount' => $this->whenCounted('bookAccesses'),
            'orders' => OrderSummaryResource::collection($this->whenLoaded('orders')),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
