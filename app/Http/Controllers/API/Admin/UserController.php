<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ToggleUserActiveRequest;
use App\Http\Requests\Admin\UserFilterRequest;
use App\Http\Resources\Admin\UserAdminResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class UserController extends Controller
{
    public function index(UserFilterRequest $request): AnonymousResourceCollection
    {
        $query = User::query()
            ->where('user_type', User::TYPE_STUDENT)
            ->withCount(['orders', 'courseAccesses', 'bookAccesses']);

        if ($request->search() !== null) {
            $search = '%'.$request->search().'%';
            $query->where(function ($subQuery) use ($search): void {
                $subQuery->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        if ($request->isActive() !== null) {
            $query->where('is_active', $request->isActive());
        }

        if ($request->createdAfter() !== null) {
            $query->whereDate('created_at', '>=', $request->createdAfter());
        }

        if ($request->createdBefore() !== null) {
            $query->whereDate('created_at', '<=', $request->createdBefore());
        }

        return UserAdminResource::collection(
            $query->orderBy($request->sortColumnName(), $request->sortDirectionName())
                ->paginate($request->perPage())
        );
    }

    public function show(User $user): UserAdminResource
    {
        abort_unless($user->isStudent(), Response::HTTP_NOT_FOUND);

        return UserAdminResource::make(
            $user->load([
                'orders' => fn ($query) => $query->latest()->limit(10),
            ])->loadCount(['orders', 'courseAccesses', 'bookAccesses'])
        );
    }

    public function updateActive(ToggleUserActiveRequest $request, User $user): UserAdminResource
    {
        abort_unless($user->isStudent(), Response::HTTP_NOT_FOUND);

        $user->forceFill([
            'is_active' => $request->boolean('isActive'),
        ])->save();

        return UserAdminResource::make($user->refresh()->loadCount(['orders', 'courseAccesses', 'bookAccesses']));
    }
}
