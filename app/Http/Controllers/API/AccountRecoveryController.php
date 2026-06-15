<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\PasswordResetService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

final class AccountRecoveryController extends Controller
{
    public function __construct(private readonly PasswordResetService $passwords) {}

    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        $this->passwords->sendResetLink((string) $request->validated('email'));

        return ApiResponse::success([
            'message' => __(Password::RESET_LINK_SENT),
        ]);
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $this->passwords->reset($request->data());

        return ApiResponse::success([
            'message' => __(Password::PASSWORD_RESET),
        ]);
    }
}
