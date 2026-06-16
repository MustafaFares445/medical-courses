<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderFilterRequest;
use App\Http\Resources\Admin\OrderAdminResource;
use App\Models\Order;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class OrderController extends Controller
{
    public function index(OrderFilterRequest $request): AnonymousResourceCollection
    {
        $query = Order::query()
            ->with('user')
            ->withCount(['items', 'payments']);

        if ($request->search() !== null) {
            $search = '%'.$request->search().'%';
            $query->where('order_number', 'like', $search);
        }

        if ($request->status() !== null) {
            $query->where('status', $request->status());
        }

        if ($request->userId() !== null) {
            $query->where('user_id', $request->userId());
        }

        if ($request->createdAfter() !== null) {
            $query->whereDate('created_at', '>=', $request->createdAfter());
        }

        if ($request->createdBefore() !== null) {
            $query->whereDate('created_at', '<=', $request->createdBefore());
        }

        return OrderAdminResource::collection(
            $query->orderBy($request->sortColumnName(), $request->sortDirectionName())
                ->paginate($request->perPage())
        );
    }

    public function show(Order $order): OrderAdminResource
    {
        return OrderAdminResource::make(
            $order->load(['user', 'items', 'payments'])->loadCount(['items', 'payments'])
        );
    }
}
