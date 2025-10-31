<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ModelController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');


Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'can:is-admin'])->name('dashboard');


Route::get('language/{locale}', [LanguageController::class, 'switch'])
    ->whereIn('locale', config('app.supported_locales'))
    ->name('language.switch');

Route::resource('listings', ListingController::class)
    ->middleware(['auth', 'verified']);
Route::resource('categories', CategoryController::class)->only([
    'index',
    'show'
]);

Route::resource('categories', CategoryController::class)->except([
    'index',
    'show'
])->middleware(['auth',  'verified', 'can:is-admin']);

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
