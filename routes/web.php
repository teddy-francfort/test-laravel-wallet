<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RecurringTransferController;
use App\Http\Controllers\SendMoneyController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::post('/send-money', [SendMoneyController::class, '__invoke'])->name('send-money');
    Route::resource('recurringtransfers', RecurringTransferController::class)
        ->parameters(['recurringtransfers' => 'recurringTransfer'])
        ->only(['index', 'store', 'destroy']);
});

require __DIR__.'/auth.php';
