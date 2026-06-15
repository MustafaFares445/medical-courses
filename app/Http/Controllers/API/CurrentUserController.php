<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AccountService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CurrentUserController extends Controller
{
    public function __construct(private readonly AccountService $accounts) {}

    public function show(Request $request): JsonResponse
    {
        return ApiResponse::success([
            'user' => UserResource::make($request->user()),
        ]);
    }

    public function update(ProfileRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user = $this->accounts->updateProfile($user, $request->data());

        return ApiResponse::success([
            'user' => UserResource::make($user),
        ]);
    }
}
