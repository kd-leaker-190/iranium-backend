<?php

use Illuminate\Support\Facades\Route;
use Modules\MCQ\Http\Controllers\MCQController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(MCQController::class)->name('mcq.')->group(function () {
        Route::post('/mcq/{mcq}', 'answer');
    });

});
