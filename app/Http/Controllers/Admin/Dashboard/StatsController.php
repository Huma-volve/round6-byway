<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Dashboard\StatsService;

use Illuminate\Support\Facades\Log;
use Exception;

class StatsController extends Controller
{
    protected $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function index(Request $request)
    {
        try {
            $stats = $this->statsService->getDashboardStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Dashboard statistics fetched successfully.'
            ]);
        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('Dashboard stats error: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to fetch dashboard statistics. Please try again later.'
            ], 500);
        }
    }
}
