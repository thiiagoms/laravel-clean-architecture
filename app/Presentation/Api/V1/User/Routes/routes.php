<?php

use App\Presentation\Api\V1\User\Controllers\Register\RegisterUserApiController;
use Illuminate\Support\Facades\Route;

/**
 * |-----------------------------
 * | Public routes
 * |-----------------------------
 */
Route::post('register', RegisterUserApiController::class)->name('register');
