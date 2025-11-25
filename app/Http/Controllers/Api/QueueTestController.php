<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\LogRequestJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueTestController extends Controller
{
    /**
     * Dispatch a job to log the request data with 1 second delay.
     */
    public function dispatch(Request $request): JsonResponse
    {
        $requestData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => $request->all(),
        ];

        LogRequestJob::dispatch($requestData)->delay(now()->addSecond());

        return response()->json([
            'message' => 'Job dispatched successfully',
            'data' => $requestData,
            'queued_at' => now()->toDateTimeString(),
        ]);
    }
}
