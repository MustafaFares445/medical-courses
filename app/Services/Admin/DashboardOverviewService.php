<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Article;
use App\Models\Book;
use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Collection;

final class DashboardOverviewService
{
    /**
     * @return array<string, mixed>
     */
    public function getOverview(): array
    {
        $paidOrders = Order::query()->where('status', Order::STATUS_PAID);

        return [
            'totalUsers' => User::query()->count(),
            'totalCourses' => Course::query()->count(),
            'totalPublishedCourses' => Course::query()->where('status', 'published')->count(),
            'totalBooks' => Book::query()->count(),
            'totalPublishedBooks' => Book::query()->where('status', 'published')->count(),
            'totalArticles' => Article::query()->count(),
            'totalPublishedArticles' => Article::query()->where('status', 'published')->count(),
            'totalPaidOrders' => (clone $paidOrders)->count(),
            'totalRevenue' => (string) (clone $paidOrders)->sum('total'),
            'recentOrders' => $this->recentOrders(),
            'recentPayments' => $this->recentPayments(),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function recentOrders(): Collection
    {
        return Order::query()
            ->with('user')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Order $order): array => [
                'id' => $order->id,
                'orderNumber' => $order->order_number,
                'status' => $order->status,
                'total' => $order->total,
                'currency' => $order->currency,
                'customer' => $order->user === null ? null : [
                    'id' => $order->user->id,
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                ],
                'paidAt' => $order->paid_at?->toISOString(),
                'createdAt' => $order->created_at?->toISOString(),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function recentPayments(): Collection
    {
        return Payment::query()
            ->with('order.user')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Payment $payment): array => [
                'id' => $payment->id,
                'orderId' => $payment->order_id,
                'provider' => $payment->provider,
                'status' => $payment->status,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'providerSessionId' => $payment->provider_session_id,
                'processedAt' => $payment->processed_at?->toISOString(),
                'createdAt' => $payment->created_at?->toISOString(),
            ]);
    }
}
