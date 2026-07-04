<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserFilterRequest;
use App\Http\Resources\Admin\UserAdminResource;
use App\Models\User;
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

    public function show(User $admin): UserAdminResource
    {
        abort_unless($admin->user_type === 'admin', Response::HTTP_NOT_FOUND);

        return UserAdminResource::make($admin->loadCount(['orders', 'courseAccesses', 'bookAccesses']));
    }

    public function store(): never
    {
        abort(Response::HTTP_NOT_IMPLEMENTED, 'Admin creation is not enabled yet.');
    }

    public function update(): never
    {
        abort(Response::HTTP_NOT_IMPLEMENTED, 'Admin updates are not enabled yet.');
    }

    public function destroy(): never
    {
        abort(Response::HTTP_NOT_IMPLEMENTED, 'Admin deletion is not enabled yet.');
    }
}
