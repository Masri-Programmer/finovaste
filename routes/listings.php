<?php

use App\Http\Controllers\ListingController;
use Illuminate\Support\Facades\Route;

Route::controller(ListingController::class)
    ->prefix('listings')
    ->name('listings.')
    ->group(function () {

        Route::get('/', 'index')->name('index');

        Route::middleware(['auth', 'verified'])->group(function () {

            Route::get('/create', 'create')->name('create');
            Route::get('/liked', 'liked')->name('liked');

            Route::post('/', 'store')->name('store');
            Route::post('/{listing}/like', 'like')->name('like');
            Route::delete('/{listing}/unlike', 'unlike')->name('unlike');

            Route::get('/{listing}/edit', 'edit')->name('edit');
            Route::match(['put', 'patch'], '/{listing}', 'update')->name('update');
            Route::delete('/{listing}', 'destroy')->name('destroy');

            Route::get('/users/{user}', 'userListings')->name('users.index');;
        });

        Route::get('/{listing}', 'show')->name('show');
    });
