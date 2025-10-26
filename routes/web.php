<?php

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ModelController;
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

Route::resource('{model}', ModelController::class)
    ->only(['index', 'show'])
    ->names('dynamic')
    ->parameters(['{model}' => 'item']);

// Admin-only: create, store, edit, update, destroy
Route::middleware('is.admin')->group(function () {
    Route::resource('{model}', ModelController::class)
        ->except(['index', 'show'])
        ->names('dynamic')
        ->parameters(['{model}' => 'item']);
});
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
