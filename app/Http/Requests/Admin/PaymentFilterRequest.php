<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

final class PaymentFilterRequest extends AdminFilterRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return array_merge($this->commonRules('amount,-amount,processedAt,-processedAt,createdAt,-createdAt'), [
            'filter.status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'filter.provider' => ['sometimes', 'nullable', 'string', 'max:50'],
            'filter.orderId' => ['sometimes', 'nullable', 'integer', 'exists:orders,id'],
        ]);
    }

    public function status(): ?string
    {
        return $this->stringFilter('status');
    }

    public function provider(): ?string
    {
        return $this->stringFilter('provider');
    }

    public function orderId(): ?int
    {
        return $this->integerFilter('orderId');
    }

    public function sortColumnName(): string
    {
        return $this->sortColumn([
            'amount' => 'amount',
            'processedAt' => 'processed_at',
            'createdAt' => 'created_at',
        ], 'created_at', '-createdAt');
    }

    public function sortDirectionName(): string
    {
        return $this->sortDirection('-createdAt');
    }
}
