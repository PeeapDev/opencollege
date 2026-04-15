<?php

use App\Modules\Finance\Controllers\InvoiceController;
use App\Modules\Finance\Controllers\PaymentController;
use App\Modules\Finance\Controllers\PeeapPayController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('invoices', InvoiceController::class);
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store']);

    // PeeapPay Online Payments
    Route::get('/pay/{invoice}', [PeeapPayController::class, 'payInvoice'])->name('peeappay.pay');
    Route::post('/pay/initialize', [PeeapPayController::class, 'initializePayment'])->name('peeappay.initialize');
    Route::get('/pay/callback', [PeeapPayController::class, 'callback'])->name('peeappay.callback');
    Route::get('/peeappay/transactions', [PeeapPayController::class, 'transactions'])->name('peeappay.transactions');
});

// Webhook (no auth — called by PeeapPay servers)
Route::post('/webhooks/peeappay', [PeeapPayController::class, 'webhook'])->name('peeappay.webhook');
