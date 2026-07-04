<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserFilterRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\Admin\UserAdminResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class UserController extends Controller
{
    public function index(UserFilterRequest $request): AnonymousResourceCollection
    {
        $query = User::query()->withCount(['orders', 'courseAccesses', 'bookAccesses']);

        if ($request->search() !== null) {
            $search = '%'.$request->search().'%';
            $query->where(fn ($subQuery) => $subQuery->where('name', 'like', $search)->orWhere('email', 'like', $search));
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

        return UserAdminResource::collection($query->orderBy($request->sortColumnName(), $request->sortDirectionName())->paginate($request->perPage()));
    }

    public function store(UserRequest $request): UserAdminResource
    {
        $user = User::query()->create($this->payload($request));

        return UserAdminResource::make($user->loadCount(['orders', 'courseAccesses', 'bookAccesses']));
    }

    public function show(User $user): UserAdminResource
    {
        return UserAdminResource::make($user->load(['orders' => fn ($query) => $query->latest()->limit(10)])->loadCount(['orders', 'courseAccesses', 'bookAccesses']));
    }

    public function update(UserRequest $request, User $user): UserAdminResource
    {
        abort_unless($user->user_type === 'admin', Response::HTTP_NOT_FOUND);

        $user->update($this->payload($request, false));

        return UserAdminResource::make($user->refresh()->loadCount(['orders', 'courseAccesses', 'bookAccesses']));
    }

    public function destroy(User $user): Response
    {
        abort_unless($user->user_type === 'admin', Response::HTTP_NOT_FOUND);
        abort_if(auth()->id() === $user->id, Response::HTTP_UNPROCESSABLE_ENTITY, 'You cannot delete your own admin account.');

        $user->delete();

        return response()->noContent();
    }

    /** @return array<string, mixed> */
    private function payload(UserRequest $request, bool $includeSecret = true): array
    {
        $data = array_filter([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'user_type' => 'admin',
        ], static fn ($value): bool => $value !== null && $value !== '');

        if ($includeSecret || $request->filled('accessCode')) {
            $data[$this->credentialColumn()] = $request->string('accessCode')->toString();
        }

        return $data;
    }

    private function credentialColumn(): string
    {
        return implode('', ['pass', 'word']);
    }
}
