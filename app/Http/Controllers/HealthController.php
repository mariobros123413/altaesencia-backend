<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $databaseStatus = 'up';
        $statusCode = 200;

        try {
            DB::select('SELECT 1');
        } catch (Throwable $exception) {
            $databaseStatus = 'down';
            $statusCode = 503;
        }

        return response()->json([
            'status' => $databaseStatus === 'up' ? 'ok' : 'degraded',
            'app' => config('app.name'),
            'environment' => app()->environment(),
            'timestamp' => now()->toIso8601String(),
            'checks' => [
                'database' => $databaseStatus,
            ],
        ], $statusCode);
    }
}
