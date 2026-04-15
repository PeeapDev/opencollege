<?php

use App\Modules\Communication\Controllers\NoticeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('notices', NoticeController::class)->except(['show']);

    // Messaging
    Route::get('/messages/inbox', [NoticeController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/sent', [NoticeController::class, 'sent'])->name('messages.sent');
    Route::get('/messages/compose', [NoticeController::class, 'compose'])->name('messages.compose');
    Route::post('/messages/send', [NoticeController::class, 'sendMessage'])->name('messages.send');
    Route::get('/messages/{message}', [NoticeController::class, 'readMessage'])->name('messages.read');
});
