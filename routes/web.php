<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('language/{locale}', [LanguageController::class, 'switch'])
    ->whereIn('locale', config('app.supported_locales'))
    ->name('language.switch');

Route::resource('categories', CategoryController::class)->only([
    'index',
    'show'
]);


require __DIR__ . '/listings.php';
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
