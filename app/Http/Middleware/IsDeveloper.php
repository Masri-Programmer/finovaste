<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsDeveloper
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow if debug mode is on OR user has developer/admin role
        if (config('app.debug') || $request->user()?->hasRole(['developer', 'admin'])) {
            return $next($request);
        }

        abort(403, 'Access denied. Developer privileges required.');
    }
}
