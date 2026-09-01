<?php

use Illuminate\Support\Facades\Route;
use Modules\MCQ\Http\Controllers\MCQController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('mcqs', MCQController::class)->names('mcq');
});
