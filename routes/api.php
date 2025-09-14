<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\Dashboard\StatsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\InstructorsController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\CoursesController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\StudentProfileController;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentMethodController;  
use App\Http\Controllers\PaymentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::middleware(['auth:sanctum'])->prefix('payment')->group(function(){
//Payment methods
Route::post('/payment-methods', [PaymentMethodController::class, 'store']);
Route::get('/user-payment-methods', [PaymentMethodController::class, 'listPaymentMethods']);
//Payment checkout
Route::post('/checkout',[PaymentController::class,'checkout']);
//View Payment History
Route::get('payment-history',[Paymentcontroller::class,'PaymentHistory']);

});




Route::prefix('reviews')->group (function(){

    Route::get('/',[ReviewController::class,'index']);
    Route::get('/{id}',[ReviewController::class,'show']);
    Route::delete('/{id}',[ReviewController::class,'destroy']);
    Route::patch('/{id}/status',[ReviewController::class,'updateStatus']);

});
//Auth
Route::post('register',[AuthController::class,'register']);
Route::post('verify-email', [AuthController::class, 'verifyEmail']); 
Route::post('login',[AuthController::class,'login']);
Route::post('resend-verification', [AuthController::class, 'resendVerification']);
Route::post('logout',[AuthController::class,'logout'])->middleware('auth:sanctum');
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);



// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/dashboard/stats', [StatsController::class, 'index']);
});
// Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {});

Route::prefix('admin')->group(function () {
    // Users management
    Route::get('users', [UsersController::class, 'index']);
    Route::get('users/{user}', [UsersController::class, 'show']);
    Route::patch('users/{user}', [UsersController::class, 'update']);
    Route::patch('users/{user}/status', [UsersController::class, 'setStatus']);
    Route::post('users/{user}/disable', [UsersController::class, 'disable']);
    Route::delete('users/{user}', [UsersController::class, 'destroy']);

    // Instructors management
    Route::post('instructors', [InstructorsController::class, 'store']);
    Route::get('instructors', [InstructorsController::class, 'index']);
    Route::get('instructors/{instructor}', [InstructorsController::class, 'show']);
    Route::patch('instructors/{instructor}', [InstructorsController::class, 'update']);
    Route::patch('instructors/{instructor}/profile', [InstructorsController::class, 'updateProfile']);
    Route::patch('instructors/{instructor}/status', [InstructorsController::class, 'setStatus']);
    Route::post('instructors/{instructor}/disable', [InstructorsController::class, 'disable']);
    Route::delete('instructors/{instructor}', [InstructorsController::class, 'destroy']);

    // Courses management
    Route::get('courses', [CoursesController::class, 'index']);
    Route::get('courses/{course}', [CoursesController::class, 'show']);
    Route::patch('courses/{course}', [CoursesController::class, 'update']);
    Route::patch('courses/{course}/status', [CoursesController::class, 'setStatus']);
    Route::post('courses/{course}/approve', [CoursesController::class, 'approve']);
    Route::post('courses/{course}/archive', [CoursesController::class, 'archive']);
    Route::delete('courses/{course}', [CoursesController::class, 'destroy']);
});


//StudentProfile
Route::get('student/profile',[StudentProfileController::class,'show'])->middleware('auth:sanctum');
Route::post('student/profile',[StudentProfileController::class,'update'])->middleware('auth:sanctum');

//student's enrolled courses
Route::get('my-courses',[EnrollmentController::class,'index'])->middleware('auth:sanctum');

//close account
Route::delete('account/close', [AccountController::class, 'closeAccount'])->middleware('auth:sanctum');
Route::post('reactivate-account',[AccountController::class,'reactivate'])->middleware('auth:sanctum');



// Route::get('/admin/dashboard/stats', [StatsController::class, 'index']);
