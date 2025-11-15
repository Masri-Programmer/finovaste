<?php

use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ListingController as AdminListingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {

    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Route::resource('categories', CategoryController::class);

    Route::get('/settings', [SettingsController::class, 'show'])
        ->name('settings.show');

    Route::patch('/settings', [SettingsController::class, 'update'])
        ->name('settings.update');

    Route::resource('listings', AdminListingController::class);
    Route::resource('users', AdminUserController::class);
    Route::resource('categories', AdminCategoryController::class);
});
