<?php

use App\Modules\Core\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| DPG Criterion 6 — Data portability. Every exported endpoint returns CSV
| by default; append ?format=json for JSON.
|
| Auth: uses standard Laravel session auth (web middleware) so existing
| logged-in users can export via the admin UI without an API token. A
| future release will add Sanctum personal-access tokens for CLI/third-
| party clients.
|
*/

Route::middleware(['web', 'auth', 'throttle:60,1'])->prefix('api')->group(function () {

    // Admin exports — tenant-scoped to the caller's current institution
    Route::get('/export/students',   [ExportController::class, 'students']);
    Route::get('/export/grades',     [ExportController::class, 'grades']);
    Route::get('/export/attendance', [ExportController::class, 'attendance']);
    Route::get('/export/finance',    [ExportController::class, 'finance']);

    // Data-subject access — returns only the caller's own records
    Route::get('/me/export', [ExportController::class, 'myData']);
});
