<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
});

// Public QR Code page (for office monitor display)
Route::get('/qrcode', [DashboardController::class, 'qrcode'])->name('qrcode');
Route::get('/attendance/qrcode', [DashboardController::class, 'qrcode'])->name('qrcode.public');

Route::middleware('auth:admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Attendance
    Route::get('/attendance/qrcode', [DashboardController::class, 'qrcode'])->name('admin.attendance.qrcode');
    Route::get('/attendance', [DashboardController::class, 'attendanceHistory'])->name('admin.attendance.index');
    Route::get('/attendance/history', [DashboardController::class, 'attendanceHistory'])->name('admin.attendance.history');

    // Interns
    Route::get('/interns', [DashboardController::class, 'interns'])->name('admin.interns.index');
    Route::get('/interns/create', [DashboardController::class, 'createIntern'])->name('admin.interns.create');
    Route::post('/interns', [DashboardController::class, 'storeIntern'])->name('admin.interns.store');
    Route::get('/interns/{id}/edit', [DashboardController::class, 'editIntern'])->name('admin.interns.edit');
    Route::put('/interns/{id}', [DashboardController::class, 'updateIntern'])->name('admin.interns.update');
    Route::delete('/interns/{id}', [DashboardController::class, 'destroyIntern'])->name('admin.interns.destroy');

    // Reports
    Route::get('/reports', [DashboardController::class, 'reports'])->name('admin.reports.index');
    Route::post('/reports/{id}/approve', [DashboardController::class, 'approveReport'])->name('admin.reports.approve');
    Route::get('/reports/export', [DashboardController::class, 'exportReports'])->name('admin.reports.export');

    // Izin
    Route::get('/izin', [DashboardController::class, 'izinRequests'])->name('admin.izin.index');
    Route::post('/izin/{id}/review', [DashboardController::class, 'reviewIzin'])->name('admin.izin.review');

    // Settings
    Route::get('/settings', [DashboardController::class, 'settings'])->name('admin.settings');
    Route::put('/settings', [DashboardController::class, 'updateSettings'])->name('admin.settings.update');
});
