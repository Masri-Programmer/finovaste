<?php

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ModelController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Homepage');
})->name('home');

Route::get('listing/create', function () {
    return Inertia::render('listings/Create');
})->name('listing.create');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'can:view-dashboard'])->name('dashboard');

Route::resource('listings', ListingController::class)->middleware(['auth', 'verified']);

Route::get('language/{locale}', [LanguageController::class, 'switch'])
    ->whereIn('locale', config('app.supported_locales'))
    ->name('language.switch');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
