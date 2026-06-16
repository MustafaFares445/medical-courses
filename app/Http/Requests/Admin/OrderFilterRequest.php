<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

final class OrderFilterRequest extends AdminFilterRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return array_merge($this->commonRules('orderNumber,-orderNumber,total,-total,createdAt,-createdAt,paidAt,-paidAt'), [
            'filter.status' => ['sometimes', 'nullable', 'in:pending,paid,failed,cancelled,expired'],
            'filter.userId' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
        ]);
    }

    public function status(): ?string
    {
        return $this->stringFilter('status');
    }

    public function userId(): ?int
    {
        return $this->integerFilter('userId');
    }

    public function sortColumnName(): string
    {
        return $this->sortColumn([
            'orderNumber' => 'order_number',
            'total' => 'total',
            'createdAt' => 'created_at',
            'paidAt' => 'paid_at',
        ], 'created_at', '-createdAt');
    }

    public function sortDirectionName(): string
    {
        return $this->sortDirection('-createdAt');
    }
}
