<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Stevebauman\Location\Facades\Location;

class SetLanguageMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        //     app()->setLocale($request->getPreferredLanguage());
        $locale = Session::get('locale');
        if (!$locale && auth()->check()) {
            $locale = auth()->user()->locale;
        }

        if (!$locale) {
             $ip = $request->ip();
             // For testing:
             // $ip = '103.111.97.0'; 
             
             if ($position = Location::get($ip)) {
                $countryCode = $position->countryCode;
                $locale = match(strtolower($countryCode)) {
                    'es' => 'es',
                    'de', 'at', 'ch' => 'de',
                    default => 'en',
                };
                
                // Validate against supported locales
                if (!in_array($locale, config('app.supported_locales', ['en', 'de', 'es']))) {
                    $locale = null; // Fallback to default
                } else {
                     Session::put('locale', $locale);
                }
             }
        }

        if (!$locale) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);
        return $next($request);
    }
}
