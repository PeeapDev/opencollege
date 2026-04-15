<?php

use App\Modules\Staff\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('staff', StaffController::class);
});
