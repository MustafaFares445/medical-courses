<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return ApiResponse::error('Unauthenticated.', Response::HTTP_UNAUTHORIZED);
        }

        $canAccessDashboard = $user->is_active === true && $user->isAdmin();

        if ($canAccessDashboard === false) {
            return ApiResponse::error('Forbidden.', Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
