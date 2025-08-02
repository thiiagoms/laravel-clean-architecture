<?php

use App\Presentation\Http\Api\V1\Task\Controllers\TaskApiController;
use Illuminate\Support\Facades\Route;

Route::apiResource('', TaskApiController::class);
