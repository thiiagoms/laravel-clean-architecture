<?php

use App\Http\Controllers\Api\Task\TaskApiController;
use Illuminate\Support\Facades\Route;

/**
 * |---------------------------------------
 * | Protected routes
 * |---------------------------------------
 */
Route::middleware('auth:api')->group(function (): void {

    /**
     * |---------------------------------------
     * | Task API CRUD
     * |---------------------------------------
     */
    Route::apiResource('task', TaskApiController::class);
});
