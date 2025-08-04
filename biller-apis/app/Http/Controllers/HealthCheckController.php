<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HealthCheckController extends Controller
{
    /**
     * Perform a simple health check of the application and its services.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        try {
            // Check if the database connection is working
            DB::connection()->getPdo();

            return response()->json([
                'status' => 'success',
                'message' => 'Application is healthy',
                'database_connection' => 'OK'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Application is not healthy',
                'error_details' => $e->getMessage(),
                'database_connection' => 'FAILED'
            ], 500);
        }
    }
}