<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\StripeWebhookService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JsonException;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

final class StripeWebhookController extends Controller
{
    public function __construct(private readonly StripeWebhookService $webhooks) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $event = $this->webhooks->eventFromPayload(
                payload: $request->getContent(),
                signature: $request->headers->get('Stripe-Signature'),
            );
        } catch (JsonException|SignatureVerificationException|UnexpectedValueException) {
            return ApiResponse::error('Invalid webhook payload.', 400);
        }

        $this->webhooks->process($event);

        return ApiResponse::success(['received' => true]);
    }
}
