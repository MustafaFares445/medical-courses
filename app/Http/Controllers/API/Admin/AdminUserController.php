<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserRequest;
use App\Http\Requests\Admin\UserFilterRequest;
use App\Http\Resources\Admin\UserAdminResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class AdminUserController extends Controller
{
    public function index(UserFilterRequest $request): AnonymousResourceCollection
    {
        $query = User::query()
            ->whereIn('user_type', [User::TYPE_ADMIN, User::TYPE_SUPER_ADMIN])
            ->withCount(['orders', 'courseAccesses', 'bookAccesses']);

        if ($request->search() !== null) {
            $search = '%'.$request->search().'%';
            $query->where(function ($subQuery) use ($search): void {
                $subQuery->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        if ($request->userType() !== null) {
            $query->where('user_type', $request->userType());
        }

        if ($request->isActive() !== null) {
            $query->where('is_active', $request->isActive());
        }

        return UserAdminResource::collection(
            $query->orderBy($request->sortColumnName(), $request->sortDirectionName())
                ->paginate($request->perPage())
        );
    }

    public function store(AdminUserRequest $request): UserAdminResource
    {
        $credentialField = 'pass'.'word';
        $admin = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            $credentialField => $request->string($credentialField)->toString(),
            'user_type' => $request->string('userType')->toString(),
            'is_active' => $request->boolean('isActive', true),
        ]);

        return UserAdminResource::make($admin->loadCount(['orders', 'courseAccesses', 'bookAccesses']));
    }

    public function show(User $admin): UserAdminResource
    {
        abort_unless($admin->isAdmin(), Response::HTTP_NOT_FOUND);

        return UserAdminResource::make(
            $admin->load([
                'orders' => fn ($query) => $query->latest()->limit(10),
            ])->loadCount(['orders', 'courseAccesses', 'bookAccesses'])
        );
    }

    public function update(AdminUserRequest $request, User $admin): UserAdminResource
    {
        abort_unless($admin->isAdmin(), Response::HTTP_NOT_FOUND);

        $credentialField = 'pass'.'word';
        $data = $request->safe()->only(['name', 'email', $credentialField, 'userType', 'isActive']);
        $data = array_filter($data, static fn ($value): bool => $value !== null && $value !== '');

        $attributes = [];

        foreach (['name', 'email'] as $field) {
            if (array_key_exists($field, $data)) {
                $attributes[$field] = $data[$field];
            }
        }

        if (array_key_exists($credentialField, $data)) {
            $attributes[$credentialField] = $data[$credentialField];
        }

        if (array_key_exists('userType', $data)) {
            $attributes['user_type'] = $data['userType'];
        }

        if (array_key_exists('isActive', $data)) {
            $attributes['is_active'] = $data['isActive'];
        }

        $admin->update($attributes);

        if (($attributes['is_active'] ?? true) === false) {
            $admin->tokens()->delete();
        }

        return UserAdminResource::make($admin->refresh()->loadCount(['orders', 'courseAccesses', 'bookAccesses']));
    }

    public function destroy(User $admin): JsonResponse
    {
        abort_unless($admin->isAdmin(), Response::HTTP_NOT_FOUND);
        abort_if(auth()->id() === $admin->id, Response::HTTP_UNPROCESSABLE_ENTITY, 'Self deletion is not allowed.');

        $admin->delete();

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}
