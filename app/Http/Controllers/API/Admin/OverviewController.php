<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardOverviewService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

final class OverviewController extends Controller
{
    public function __invoke(DashboardOverviewService $service): JsonResponse
    {
        return ApiResponse::success($service->getOverview());
    }
}
