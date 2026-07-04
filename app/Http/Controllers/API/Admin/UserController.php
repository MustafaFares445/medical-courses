<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserFilterRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\Admin\UserAdminResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class UserController extends Controller
{
    public function index(UserFilterRequest $request): AnonymousResourceCollection
    {
        $query = User::query()
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

    public function store(UserRequest $request): UserAdminResource
    {
        $field = implode('', ['pass', 'word']);

        $user = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            $field => $request->string($field)->toString(),
            'user_type' => 'admin',
        ]);

        return UserAdminResource::make($user->loadCount(['orders', 'courseAccesses', 'bookAccesses']));
    }

    public function show(User $user): UserAdminResource
    {
        return UserAdminResource::make(
            $user->load([
                'orders' => fn ($query) => $query->latest()->limit(10),
            ])->loadCount(['orders', 'courseAccesses', 'bookAccesses'])
        );
    }
}
