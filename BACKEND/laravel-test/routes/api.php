<?php

use App\Http\Controllers\PaymentController;

use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::post('payments/easymoney', [PaymentController::class, 'payWithEasyMoney']);
    Route::post('payments/superwalletz', [PaymentController::class, 'payWithSuperWalletz']);
    Route::post('webhook/superwalletz', [PaymentController::class, 'webhookSuperWalletz'])->name('webhook.superwalletz');
});
