<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Stevebauman\Location\Facades\Location;

class SetLocaleOnLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        /** @var \App\Models\User $user */
        $user = $event->user;
        $ip = request()->ip();
        
        // For local testing, you might want to hardcode an IP if it's 127.0.0.1
        // $ip = '103.111.97.0'; // Example IP

        if ($position = Location::get($ip)) {
            $countryCode = $position->countryCode;
            
            // Map country codes to your supported locales
            $locale = match(strtolower($countryCode)) {
                'es' => 'es',
                'de', 'at', 'ch' => 'de', // German for Germany, Austria, Switzerland (example)
                default => 'en',
            };

            // Only update if different and valid
            if ($user->locale !== $locale && in_array($locale, config('app.supported_locales', ['en', 'de', 'es']))) {
                $user->locale = $locale;
                $user->save();
            }
            
            Session::put('locale', $locale);
        }
    }
}
