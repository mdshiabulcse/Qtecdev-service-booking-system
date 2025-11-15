<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/check-auth', [AuthController::class, 'checkAuth']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard routes - accessible to all authenticated users
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/recent-activities', [DashboardController::class, 'getRecentActivities']);
    Route::get('/dashboard/monthly-chart', [DashboardController::class, 'getMonthlyChart']);
    Route::get('/dashboard/class-performance', [DashboardController::class, 'getClassPerformance']);

    // Student routes - accessible to teachers and admins
    Route::middleware('teacher')->group(function () {
        Route::apiResource('students', StudentController::class);
        Route::get('/students/{student}/attendance', [StudentController::class, 'getAttendance']);
        Route::get('/students/class/{class}/section/{section}', [StudentController::class, 'getByClassSection']);

        // Attendance routes
        Route::post('/attendance/bulk', [AttendanceController::class, 'recordBulk']);
        Route::get('/attendance/today', [AttendanceController::class, 'getTodayAttendance']);
        Route::get('/attendance/monthly-report', [AttendanceController::class, 'getMonthlyReport']);
        Route::get('/attendance/class/{class}/section/{section}', [AttendanceController::class, 'getClassAttendance']);
        Route::get('/attendance/student/{studentId}', [AttendanceController::class, 'getStudentAttendance']);
    });

    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', [AdminController::class, 'getAllUsers']);
        Route::post('/admin/users', [AdminController::class, 'createUser']);
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser']);
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser']);

        Route::get('/admin/system-stats', [AdminController::class, 'getSystemStats']);
    });
});
