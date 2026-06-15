<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Checkout\CheckoutData;
use App\Models\Book;
use App\Models\BookAccess;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class CheckoutService
{
    public function __construct(private readonly StripeCheckoutService $stripe) {}

    public function create(User $user, CheckoutData $data): array
    {
        $preparedItems = $this->prepareItems($user, $data);

        $order = DB::transaction(function () use ($user, $preparedItems): Order {
            $total = collect($preparedItems)->sum('price');
            $currency = strtoupper((string) $preparedItems[0]['currency']);

            $order = Order::query()->create([
                'user_id' => $user->id,
                'order_number' => $this->newOrderNumber(),
                'status' => Order::STATUS_PENDING,
                'subtotal' => $total,
                'total' => $total,
                'currency' => $currency,
            ]);

            foreach ($preparedItems as $item) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'item_type' => $item['type'],
                    'item_id' => $item['id'],
                    'title_snapshot' => $item['title'],
                    'price_snapshot' => $item['price'],
                    'currency' => $item['currency'],
                ]);
            }

            return $order->load('items');
        });

        $session = $this->stripe->createSession(
            order: $order,
            lineItems: $this->lineItems($preparedItems),
            successUrl: $data->successUrl,
            cancelUrl: $data->cancelUrl,
        );

        $order->forceFill(['checkout_session_id' => $session['id']])->save();

        Payment::query()->create([
            'order_id' => $order->id,
            'provider' => 'stripe',
            'provider_session_id' => $session['id'],
            'status' => 'pending',
            'amount' => $order->total,
            'currency' => $order->currency,
        ]);

        return [
            'order' => $order->fresh(['items', 'payments']),
            'checkoutSessionId' => $session['id'],
            'checkoutUrl' => $session['url'],
        ];
    }

    private function prepareItems(User $user, CheckoutData $data): array
    {
        $items = [];

        foreach ($data->items as $item) {
            $key = $item->type.':'.$item->id;

            if (array_key_exists($key, $items)) {
                continue;
            }

            if ($item->type === 'course') {
                $course = Course::query()->published()->find($item->id);

                if (! $course) {
                    throw ValidationException::withMessages(['items' => 'Selected course is not available.']);
                }

                if (CourseAccess::query()->where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
                    throw ValidationException::withMessages(['items' => 'You already own one of the selected courses.']);
                }

                $items[$key] = [
                    'type' => 'course',
                    'id' => $course->id,
                    'title' => $course->title,
                    'price' => (float) $course->price,
                    'currency' => $course->currency,
                ];
            }

            if ($item->type === 'book') {
                $book = Book::query()->published()->find($item->id);

                if (! $book) {
                    throw ValidationException::withMessages(['items' => 'Selected book is not available.']);
                }

                if (BookAccess::query()->where('user_id', $user->id)->where('book_id', $book->id)->exists()) {
                    throw ValidationException::withMessages(['items' => 'You already own one of the selected books.']);
                }

                $items[$key] = [
                    'type' => 'book',
                    'id' => $book->id,
                    'title' => $book->title,
                    'price' => (float) $book->price,
                    'currency' => $book->currency,
                ];
            }
        }

        return array_values($items);
    }

    private function lineItems(array $items): array
    {
        return collect($items)->map(fn (array $item): array => [
            'quantity' => 1,
            'price_data' => [
                'currency' => strtolower((string) $item['currency']),
                'unit_amount' => (int) round((float) $item['price'] * 100),
                'product_data' => [
                    'name' => $item['title'],
                    'metadata' => [
                        'item_type' => $item['type'],
                        'item_id' => (string) $item['id'],
                    ],
                ],
            ],
        ])->values()->all();
    }

    private function newOrderNumber(): string
    {
        return 'ORD-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
    }
}
