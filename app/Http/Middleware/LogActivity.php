<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        Log::info('REQUEST MASUK', [
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'payload' => $request->except(['password']),
        ]);

        $response = $next($request);

        $status = method_exists($response, 'getStatusCode')
        ? $response->getStatusCode()
        : 200;

        Log::info('REQUEST SELESAI', [
            'user_id' => $user?->id,
            'status' => $status,
        ]);

        return $response;
    }
}