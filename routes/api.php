<?php

use App\Http\Controllers\Api\Auth\AuthenticateUserApiController;
use App\Http\Controllers\Api\User\Register\RegisterUserApiController;
use Illuminate\Support\Facades\Route;

/**
 * |-----------------------------
 * | Public routes
 * |-----------------------------
 */
Route::post('register', RegisterUserApiController::class)->name('register');

Route::post('auth', AuthenticateUserApiController::class)->name('authenticate');
