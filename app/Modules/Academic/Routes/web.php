<?php

use App\Modules\Academic\Controllers\FacultyController;
use App\Modules\Academic\Controllers\DepartmentController;
use App\Modules\Academic\Controllers\ProgramController;
use App\Modules\Academic\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('faculties', FacultyController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('programs', ProgramController::class);
    Route::resource('courses', CourseController::class);
});
