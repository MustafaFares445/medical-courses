<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\Checkout\CheckoutData;
use App\Data\Checkout\CheckoutItemData;
use Illuminate\Foundation\Http\FormRequest;

final class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.type' => ['required', 'in:course,book'],
            'items.*.id' => ['required', 'integer', 'min:1'],
            'successUrl' => ['required', 'url', 'max:2048'],
            'cancelUrl' => ['required', 'url', 'max:2048'],
        ];
    }

    public function data(): CheckoutData
    {
        $payload = $this->validated();

        $items = collect($payload['items'])
            ->map(fn (array $item): CheckoutItemData => new CheckoutItemData(
                type: (string) $item['type'],
                id: (int) $item['id'],
            ))
            ->values()
            ->all();

        return new CheckoutData(
            items: $items,
            successUrl: (string) $payload['successUrl'],
            cancelUrl: (string) $payload['cancelUrl'],
        );
    }
}
