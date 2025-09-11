<?php

use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('reviews')->middleware('auth:sanctum')->group (function(){

    Route::get('/',[ReviewController::class,'index']);
    Route::get('/{id}',[ReviewController::class,'show']);
    Route::delete('/{id}',[ReviewController::class,'destroy']);
    Route::patch('/{id}/status',[ReviewController::class,'updateStatus']);

});