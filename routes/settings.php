<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance.edit');

    Route::get('settings/languages', function () {
        return Inertia::render('settings/Languages');
    })->name('languages.edit');

    Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');

    Route::get('settings/addresses', [\App\Http\Controllers\Settings\AddressController::class, 'index'])->name('addresses.index');
    Route::post('settings/addresses', [\App\Http\Controllers\Settings\AddressController::class, 'store'])->name('addresses.store');
    Route::put('settings/addresses/{address}', [\App\Http\Controllers\Settings\AddressController::class, 'update'])->name('addresses.update');
    Route::patch('settings/addresses/{address}/primary', [\App\Http\Controllers\Settings\AddressController::class, 'setPrimary'])->name('addresses.set-primary');
    Route::delete('settings/addresses/{address}', [\App\Http\Controllers\Settings\AddressController::class, 'destroy'])->name('addresses.destroy');
});
