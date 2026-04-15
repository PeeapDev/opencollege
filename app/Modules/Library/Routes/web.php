<?php

use App\Modules\Library\Controllers\LibraryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('library', LibraryController::class)->only(['index', 'create', 'store']);
});
