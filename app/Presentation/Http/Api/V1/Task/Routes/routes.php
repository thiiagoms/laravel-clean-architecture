<?php

use App\Presentation\Http\Api\V1\Task\Controllers\TaskApiController;
use Illuminate\Support\Facades\Route;

Route::controller(TaskApiController::class)->group(function (): void {
    Route::post('register', 'store')->name('register');
    Route::get('{task}', 'show')->name('task');

    Route::patch('{task}', 'update')->name('update');
    Route::put('{task}', 'update');

    Route::delete('{task}', 'destroy')->name('delete');
});
