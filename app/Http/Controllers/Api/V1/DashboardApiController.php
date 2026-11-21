<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Get aggregated dashboard statistics via REST API.
     */
    public function stats(Request $request): JsonResponse
    {
        $dateRange = $request->query('range', 'month');
        $userId = $request->query('user_id') ? (int) $request->query('user_id') : null;
        $teamId = $request->query('team_id') ? (int) $request->query('team_id') : null;

        $metrics = $this->dashboardService->getMetrics($dateRange, $userId, $teamId);
        $chartData = $this->dashboardService->getChartData($dateRange, $userId);

        return response()->json([
            'metrics' => $metrics,
            'charts' => $chartData,
            'filter' => [
                'range' => $dateRange,
                'user_id' => $userId,
                'team_id' => $teamId,
            ],
        ]);
    }
}
