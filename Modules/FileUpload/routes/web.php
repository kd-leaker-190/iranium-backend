<?php

use Illuminate\Support\Facades\Route;
use Modules\FileUpload\Http\Controllers\FileUploadController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('fileuploads', FileUploadController::class)->names('fileupload');
});
