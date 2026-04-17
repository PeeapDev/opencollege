<?php

use App\Modules\Hemis\Controllers\HemisController;
use Illuminate\Support\Facades\Route;

/**
 * HEMIS routes — only active when request is on root domain (institution id=1).
 *
 * The HemisOnRootDomain middleware (below) guards these routes so they 404
 * when hit via a tenant subdomain like njala.college.edu.sl.
 */

Route::middleware(['web', \App\Http\Middleware\HemisOnRootDomain::class])
    ->prefix('hemis')
    ->name('hemis.')
    ->group(function () {
        Route::get('/',                  [HemisController::class, 'dashboard'])->name('dashboard');
        Route::get('/institutions',      [HemisController::class, 'institutions'])->name('institutions');
        Route::get('/institutions/{institution}', [HemisController::class, 'institutionShow'])->name('institutions.show');
        Route::get('/students',          [HemisController::class, 'students'])->name('students');
        Route::get('/students/{identifier}', [HemisController::class, 'studentShow'])->name('students.show');
        Route::get('/reports',           [HemisController::class, 'reports'])->name('reports');
        Route::get('/reports/enrollment',[HemisController::class, 'reportEnrollment'])->name('reports.enrollment');
    });
