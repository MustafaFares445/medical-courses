<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LibraryResource;
use App\Models\User;
use App\Services\LibraryService;
use Illuminate\Http\Request;

final class LibraryController extends Controller
{
    public function __construct(private readonly LibraryService $library) {}

    public function __invoke(Request $request): LibraryResource
    {
        /** @var User $user */
        $user = $request->user();

        return LibraryResource::make($this->library->forUser($user));
    }
}
