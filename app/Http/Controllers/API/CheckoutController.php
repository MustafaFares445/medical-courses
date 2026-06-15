<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\CheckoutResource;
use App\Models\User;
use App\Services\CheckoutService;

final class CheckoutController extends Controller
{
    public function __construct(private readonly CheckoutService $checkout) {}

    public function store(CheckoutRequest $request): CheckoutResource
    {
        /** @var User $user */
        $user = $request->user();

        return CheckoutResource::make($this->checkout->create($user, $request->data()));
    }
}
