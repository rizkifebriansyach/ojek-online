<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\SettingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'user'])->name('user');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('booking')->group(function () {
        Route::post('price-check', [BookingController::class, 'priceCheck']);
        Route::post('/', [BookingController::class, 'store'])->name('booking');
        Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
    });

    Route::prefix('driver')->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('driver.settings');
    });
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
