<?php

use Illuminate\Support\Facades\Route;
use Modules\Task\Http\Controllers\TaskController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(TaskController::class)->prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/{task}', 'show');
        Route::post('/{task}/skip', 'skip');
    });
});
