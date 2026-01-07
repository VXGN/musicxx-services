<?php

namespace App\Http\Middleware;

use App\Models\Log;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::create([
            'log_method' => $request->method(),
            'log_url' => $request->fullUrl(),
            'log_ip' => $request->ip(),
            'log_useragent' => $request->userAgent(),
        ]);
        return $next($request);
    }
}
