<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class HandleLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.supported_locales');
        $defaultLocale = config('app.locale', 'de'); // Get default from config
        $localeToSet = $defaultLocale; // Start with the default

        // Check if a valid locale is in the session
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            if (in_array($sessionLocale, $supportedLocales, true)) {
                $localeToSet = $sessionLocale; // Use session locale
            }
        }

        // Set the application locale for this request
        App::setLocale($localeToSet);
        // App::handleLocale($localeToSet); // Or use this, both work

        return $next($request);
    }
}
