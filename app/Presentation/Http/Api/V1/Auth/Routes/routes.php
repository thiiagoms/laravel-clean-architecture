<?php

use App\Presentation\Http\Api\V1\Auth\Controllers\Authenticate\AuthenticateApiController;
use Illuminate\Support\Facades\Route;

/**
 * |-----------------------------
 * | Public routes
 * |-----------------------------
 */
Route::post('login', AuthenticateApiController::class)->name('authenticate');
