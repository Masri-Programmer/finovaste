<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        //     app()->setLocale($request->getPreferredLanguage());
        $locale = Session::get('locale');
        if (!$locale && auth()->check()) {
            $locale = auth()->user()->locale;
        }

        if (!$locale) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);
        return $next($request);
    }
}
