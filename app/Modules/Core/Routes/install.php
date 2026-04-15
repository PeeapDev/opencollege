<?php

/**
 * Installer routes — included from app/Modules/Core/Routes/web.php.
 * These routes are blocked by CheckInstallation middleware once the app
 * is installed (storage/installed lock file exists).
 */

use App\Modules\Core\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

Route::prefix('install')->name('install.')->group(function () {
    Route::get('/',             [InstallController::class, 'welcome'])->name('welcome');
    Route::get('/requirements', [InstallController::class, 'requirements'])->name('requirements');
    Route::get('/database',     [InstallController::class, 'dbForm'])->name('db');
    Route::post('/database',    [InstallController::class, 'dbSubmit'])->name('db.submit');
    Route::get('/site',         [InstallController::class, 'siteForm'])->name('site');
    Route::post('/site',        [InstallController::class, 'siteSubmit'])->name('site.submit');
    Route::get('/admin',        [InstallController::class, 'adminForm'])->name('admin');
    Route::post('/admin',       [InstallController::class, 'adminSubmit'])->name('admin.submit');
    Route::get('/finalize',     [InstallController::class, 'finalize'])->name('finalize');
    Route::post('/run',         [InstallController::class, 'run'])->name('run');
    Route::get('/done',         [InstallController::class, 'done'])->name('done');
});
