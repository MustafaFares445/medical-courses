<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AccountService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AuthController extends Controller
{
    public function __construct(private readonly AccountService $accounts) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->accounts->register($request->data());

        return ApiResponse::success([
            'user' => UserResource::make($result['user']),
            'token' => $result['accessToken'],
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->accounts->login($request->data());

        return ApiResponse::success([
            'user' => UserResource::make($result['user']),
            'token' => $result['accessToken'],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user !== null) {
            $this->accounts->logout($user);
        }

        return ApiResponse::success([
            'message' => 'Logged out successfully.',
        ]);
    }
}
