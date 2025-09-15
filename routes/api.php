<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\Dashboard\StatsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\InstructorsController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\CoursesController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//reviews
Route::prefix('reviews')->group(function () {

    Route::get('/', [ReviewController::class, 'index']);
    Route::get('/{id}', [ReviewController::class, 'show']);
    Route::delete('/{id}', [ReviewController::class, 'destroy']);
    Route::patch('/{id}/status', [ReviewController::class, 'updateStatus']);
});

//platform settings

//settings

Route::prefix('settings')->group(function () {

    Route::get('/', [SettingController::class, 'getSettings']);
    Route::put('/{id}', [SettingController::class, 'updateSettings']);
});


//category
Route::prefix('categories')->group(function () {

    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});


//  Routes for Payments
Route::apiResource('payments', PaymentController::class);

// 🔹 Routes for Reports
Route::prefix('reports')->group(function () {
    Route::get('/user-growth', [ReportsController::class, 'userGrowth']);
    Route::get('/course-revenue', [ReportsController::class, 'courseRevenue']);
    Route::get('/instructor-performance', [ReportsController::class, 'instructorPerformance']);
    Route::get('/payments/export/pdf', [ReportsController::class, 'exportPdf']);
});



//Auth
Route::post('register', [AuthController::class, 'register']);
Route::post('verify-email', [AuthController::class, 'verifyEmail']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/dashboard/stats', [StatsController::class, 'index']);
});
// Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {});

route::prefix('admin')->group(function () {
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




// Route::get('/admin/dashboard/stats', [StatsController::class, 'index']);
