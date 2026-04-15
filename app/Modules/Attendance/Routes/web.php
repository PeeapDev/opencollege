<?php

use App\Modules\Attendance\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('attendance', AttendanceController::class)->only(['index', 'create', 'store']);
});
