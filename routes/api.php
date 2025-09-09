<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\Dashboard\StatsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('register',[AuthController::class,'register']);
// Route::post('login',[AuthController::class,'login']);


Route::middleware(['auth:sanctum', 'admin'])->group(function () {});
Route::get('/admin/dashboard/stats', [StatsController::class, 'index']);
