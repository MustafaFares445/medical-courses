<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BookAccess;
use App\Models\CourseAccess;
use App\Models\Order;

final class AccessGrantingService
{
    public function grant(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            if ($item->item_type === 'course') {
                CourseAccess::query()->firstOrCreate(
                    ['user_id' => $order->user_id, 'course_id' => $item->item_id],
                    ['order_item_id' => $item->id, 'purchased_at' => now()],
                );
            }

            if ($item->item_type === 'book') {
                BookAccess::query()->firstOrCreate(
                    ['user_id' => $order->user_id, 'book_id' => $item->item_id],
                    ['order_item_id' => $item->id, 'purchased_at' => now()],
                );
            }
        }
    }
}
