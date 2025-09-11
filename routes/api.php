<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentMethodController;  
use App\Http\Controllers\PaymentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/payment-methods', [PaymentMethodController::class, 'store']);
Route::get('/user/{id}/payment-methods', [PaymentMethodController::class, 'listPaymentMethods']);

//Payment checkout
Route::post('/checkout',[PaymentController::class,'checkout']);
