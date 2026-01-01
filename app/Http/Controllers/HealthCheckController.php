<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthCheckController extends Controller
{
    /**
     * Check system health status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke()
    {
        $status = 'healthy';
        $checks = [];

        // Database check
        try {
            DB::connection()->getPdo();
            $checks['database'] = 'ok';
        } catch (\Exception $e) {
            $checks['database'] = 'failed';
            $status = 'unhealthy';
        }

        // Cache check
        try {
            Cache::put('health_check', 'test', 10);
            $value = Cache::get('health_check');
            $checks['cache'] = ($value === 'test') ? 'ok' : 'failed';
            Cache::forget('health_check');
        } catch (\Exception $e) {
            $checks['cache'] = 'failed';
            $status = 'degraded';
        }

        // Queue check (optional - checks if queue table exists)
        try {
            $queueCount = DB::table('jobs')->count();
            $checks['queue'] = 'ok';
            $checks['pending_jobs'] = $queueCount;
        } catch (\Exception $e) {
            $checks['queue'] = 'unavailable';
        }

        $response = [
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
        ];

        $httpStatus = $status === 'healthy' ? 200 : 503;

        return response()->json($response, $httpStatus);
    }
}
