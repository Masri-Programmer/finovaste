<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\ListingFaqController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\ListingSubscriptionController;
use Illuminate\Support\Facades\Route;

Route::controller(ListingController::class)
    ->prefix('listings')
    ->name('listings.')
    ->group(function () {

        Route::get('/', 'index')->name('index');

        // 🔒 AUTHENTICATED ROUTES
        Route::middleware(['auth', 'verified'])->group(function () {

            Route::get('/create', 'create')->name('create');
            Route::get('/liked', 'liked')->name('liked');
            Route::post('/', 'store')->name('store');

            Route::post('/{listing}/bid', [BidController::class, 'store'])->name('bid');
            Route::post('/{listing}/buy', [TransactionController::class, 'buyItem'])->name('buy');
            Route::post('/{listing}/donate', [TransactionController::class, 'donate'])->name('donate');
            Route::post('/{listing}/invest', [TransactionController::class, 'invest'])->name('invest');

            // Existing Listing Routes...
            Route::post('/{listing}/like', 'like')->name('like');
            Route::delete('/{listing}/unlike', 'unlike')->name('unlike');

            Route::get('/{listing}/edit', 'edit')->name('edit');
            Route::match(['put', 'patch'], '/{listing}', 'update')->name('update');
            Route::delete('/{listing}', 'destroy')->name('destroy');

            Route::get('/users/{user}', 'userListings')->name('users.index');

            // FAQ
            Route::post('/{listing}/faq', [ListingFaqController::class, 'store'])->name('faq.store');
            Route::patch('/{listing}/faq/{faq}', [ListingFaqController::class, 'update'])->name('faq.update');
            Route::delete('/{listing}/faq/{faq}', [ListingFaqController::class, 'destroy'])->name('faq.destroy');

    
            Route::post('{listing}/reviews', [ReviewController::class, 'store'])
                ->name('reviews.store');

            Route::put('/reviews/{review}', [ReviewController::class, 'update'])
                ->name('reviews.update');
        
            Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])
                ->name('reviews.destroy');

            Route::post('/{listing}/subscribe', [ListingSubscriptionController::class, 'store'])
                ->name('subscribe');
        });



        Route::get('/{listing}', 'show')->name('show');
    });
