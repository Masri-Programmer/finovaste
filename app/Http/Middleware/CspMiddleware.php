<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CspMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // This policy is for DEVELOPMENT only.
        // For production, you'll want a much stricter policy.
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " . // 'unsafe-inline' is for Vite, 'unsafe-eval' is for vue-i18n
                "style-src 'self' 'unsafe-inline'; " . // 'unsafe-inline' is for Vite HMR
                "connect-src 'self' ws://127.0.0.1:5173; " . // Allows Vite's HMR websocket
                "img-src 'self' data:; " .
                "font-src 'self'; " .
                "object-src 'none'; " .
                "frame-ancestors 'none'; " .
                "base-uri 'self';"
        );

        return $response;
    }
}
