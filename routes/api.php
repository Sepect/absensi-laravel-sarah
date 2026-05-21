<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IzinController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes - Authentication
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AuthController::class, 'adminLogin']);

// Protected routes - Interns (Mobile App) using auth:sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Attendance
    Route::post('/absensi', [AttendanceController::class, 'scan']);
    Route::get('/absensi/riwayat', [AttendanceController::class, 'history']);
    Route::get('/absensi/summary', [AttendanceController::class, 'summary']);
    Route::get('/absensi/today', [AttendanceController::class, 'today']);

    // Reports
    Route::get('/laporan-harian', [ReportController::class, 'index']);
    Route::get('/laporan-harian/{date}', [ReportController::class, 'show']);
    Route::post('/laporan-harian', [ReportController::class, 'store']);
    Route::put('/laporan-harian/{id}', [ReportController::class, 'update']);

    // Settings
    Route::get('/office-settings', [SettingsController::class, 'index']);

    // Izin
    Route::get('/izin', [IzinController::class, 'index']);
    Route::post('/izin', [IzinController::class, 'store']);
});

// Protected routes - Admin API using auth:admin-api
Route::prefix('admin')->middleware('auth:admin-api')->group(function () {
    // Admin-specific API routes can be added here
    // Example: Route::get('/dashboard-stats', [AdminApiController::class, 'stats']);
});