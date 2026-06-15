<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderFilterRequest;
use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\OrderListResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class OrderController extends Controller
{
    public function index(OrderFilterRequest $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        $query = Order::query()
            ->where('user_id', $user->id)
            ->withCount('items')
            ->orderByDesc('created_at');

        if ($request->status() !== null) {
            $query->where('status', $request->status());
        }

        return OrderListResource::collection($query->paginate($request->perPage()));
    }

    public function show(Request $request, Order $order): OrderDetailResource
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless((int) $order->user_id === (int) $user->id, 404);

        $order->load(['items', 'payments']);

        return OrderDetailResource::make($order);
    }
}
