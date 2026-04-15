<?php

use App\Modules\Student\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('students', StudentController::class);
});
