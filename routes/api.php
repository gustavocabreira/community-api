<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\GuildController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user', [UserController::class, 'me'])->name('user.current-user');

        Route::apiResource('guilds', GuildController::class)->only('store');
    });
});
