<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application locale and redirect back.
     */
    public function switch(string $locale): RedirectResponse
    {
        $supportedLocales = config('app.supported_locales');
        if (in_array($locale, $supportedLocales, true)) {
            Session::put('locale', $locale);
        }
        return back();
    }
}
