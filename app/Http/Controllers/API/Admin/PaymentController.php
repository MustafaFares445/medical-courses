<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentFilterRequest;
use App\Http\Resources\Admin\PaymentAdminResource;
use App\Models\Payment;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class PaymentController extends Controller
{
    public function index(PaymentFilterRequest $request): AnonymousResourceCollection
    {
        $query = Payment::query()->with('order.user');

        if ($request->search() !== null) {
            $search = '%'.$request->search().'%';
            $query->where(function ($subQuery) use ($search): void {
                $subQuery->where('provider_payment_id', 'like', $search)
                    ->orWhere('provider_session_id', 'like', $search)
                    ->orWhere('provider_event_id', 'like', $search);
            });
        }

        if ($request->status() !== null) {
            $query->where('status', $request->status());
        }

        if ($request->provider() !== null) {
            $query->where('provider', $request->provider());
        }

        if ($request->orderId() !== null) {
            $query->where('order_id', $request->orderId());
        }

        if ($request->createdAfter() !== null) {
            $query->whereDate('created_at', '>=', $request->createdAfter());
        }

        if ($request->createdBefore() !== null) {
            $query->whereDate('created_at', '<=', $request->createdBefore());
        }

        return PaymentAdminResource::collection(
            $query->orderBy($request->sortColumnName(), $request->sortDirectionName())
                ->paginate($request->perPage())
        );
    }

    public function show(Payment $payment): PaymentAdminResource
    {
        return PaymentAdminResource::make($payment->load('order.user'));
    }
}
