<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    public function getStats(): JsonResponse
    {
        $stats = $this->dashboardService->getDashboardStats();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function getRecentActivities(): JsonResponse
    {
        $activities = $this->dashboardService->getRecentActivities();

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    public function getMonthlyChart(): JsonResponse
    {
        $chartData = $this->dashboardService->getMonthlyAttendanceChart();

        return response()->json([
            'success' => true,
            'data' => $chartData
        ]);
    }

    public function getClassPerformance(): JsonResponse
    {
        $stats = $this->dashboardService->getDashboardStats();

        return response()->json([
            'success' => true,
            'data' => $stats['class_wise_stats'] ?? []
        ]);
    }
}
