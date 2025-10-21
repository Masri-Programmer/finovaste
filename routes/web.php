<?php

use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Homepage');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('language/{locale}', [LanguageController::class, 'switch'])
    ->whereIn('locale', config('app.supported_locales'))
    ->name('language.switch');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
