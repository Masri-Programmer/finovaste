<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Switch the application locale and redirect back.
     */
    public function switch(string $locale, Request $request): RedirectResponse
    {
        $supportedLocales = config('app.supported_locales', ['de']);
        if (in_array($locale, $supportedLocales, true)) {
            Session::put('locale', $locale);
            App::setLocale($locale);
            if ($request->user()) {
                $request->user()->update(['locale' => $locale]);
            }
        }

        return redirect()->back();
    }
}
