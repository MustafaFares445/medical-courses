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
            ->where('user_type', 'admin')
            ->withCount(['orders', 'courseAccesses', 'bookAccesses']);

        if ($request->search() !== null) {
            $search = '%'.$request->search().'%';
            $query->where(function ($subQuery) use ($search): void {
                $subQuery->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        return UserAdminResource::collection(
            $query->orderBy($request->sortColumnName(), $request->sortDirectionName())
                ->paginate($request->perPage())
        );
    }

    public function store(AdminUserRequest $request): UserAdminResource
    {
        $admin = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => $request->string('password')->toString(),
            'user_type' => 'admin',
        ]);

        return UserAdminResource::make($admin->loadCount(['orders', 'courseAccesses', 'bookAccesses']));
    }

    public function show(User $admin): UserAdminResource
    {
        abort_unless($admin->user_type === 'admin', Response::HTTP_NOT_FOUND);

        return UserAdminResource::make(
            $admin->load([
                'orders' => fn ($query) => $query->latest()->limit(10),
            ])->loadCount(['orders', 'courseAccesses', 'bookAccesses'])
        );
    }

    public function update(AdminUserRequest $request, User $admin): UserAdminResource
    {
        abort_unless($admin->user_type === 'admin', Response::HTTP_NOT_FOUND);

        $data = $request->safe()->only(['name', 'email', 'password']);
        $data = array_filter($data, static fn ($value): bool => $value !== null && $value !== '');
        $data['user_type'] = 'admin';

        $admin->update($data);

        return UserAdminResource::make($admin->refresh()->loadCount(['orders', 'courseAccesses', 'bookAccesses']));
    }

    public function destroy(User $admin): JsonResponse
    {
        abort_unless($admin->user_type === 'admin', Response::HTTP_NOT_FOUND);
        abort_if(auth()->id() === $admin->id, Response::HTTP_UNPROCESSABLE_ENTITY, 'You cannot delete your own admin account.');

        $admin->delete();

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}
