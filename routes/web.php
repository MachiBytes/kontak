<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactBookOrderController;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('home') : redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Settings routes
Route::middleware('auth')->group(function () {
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/profile', [SettingsController::class, 'updateProfile'])->name('update-profile');
        Route::put('/password', [SettingsController::class, 'updatePassword'])->name('update-password');
        Route::put('/contact-book', [SettingsController::class, 'updateContactBook'])->name('update-contact-book');
        Route::post('/user-access', [SettingsController::class, 'addUserAccess'])->name('add-user-access');
        Route::put('/user-access/{userAccess}', [SettingsController::class, 'updateUserAccess'])->name('update-user-access');
        Route::delete('/user-access/{userAccess}', [SettingsController::class, 'removeUserAccess'])->name('remove-user-access');
        Route::delete('/user-access-bulk', [SettingsController::class, 'bulkRemoveUserAccess'])->name('bulk-remove-user-access');
    });

    // Contact book ordering
    Route::put('/contact-books/order', [ContactBookOrderController::class, 'updateOrder'])->name('contact-books.update-order');

    // Contact CRUD routes
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('/create', [ContactController::class, 'create'])->name('create');
        Route::post('/', [ContactController::class, 'store'])->name('store');
        Route::get('/{contact}', [ContactController::class, 'show'])->name('show');
        Route::get('/{contact}/edit', [ContactController::class, 'edit'])->name('edit');
        Route::put('/{contact}', [ContactController::class, 'update'])->name('update');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('destroy');
    });
});
