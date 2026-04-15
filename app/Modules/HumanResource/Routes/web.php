<?php

use App\Modules\HumanResource\Controllers\HrController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('hr')->group(function () {
    Route::get('/', [HrController::class, 'dashboard'])->name('hr.dashboard');

    // Leave Management
    Route::get('/leaves', [HrController::class, 'leaveIndex'])->name('hr.leaves');
    Route::post('/leaves/{id}/approve', [HrController::class, 'leaveApprove'])->name('hr.leaves.approve');
    Route::post('/leaves/{id}/reject', [HrController::class, 'leaveReject'])->name('hr.leaves.reject');

    // Payroll
    Route::get('/payroll', [HrController::class, 'payroll'])->name('hr.payroll');
    Route::post('/payroll/run', [HrController::class, 'runPayroll'])->name('hr.payroll.run');

    // Staff Directory
    Route::get('/directory', [HrController::class, 'directory'])->name('hr.directory');
});
