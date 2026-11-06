<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CalendarController::class, 'publicCalendar']);
Route::get('/api/calendar-feed', [CalendarController::class, 'feed']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {

    // Bisa diakses semua role yang login (lihat & ajukan agenda)
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/create', [EventController::class, 'create'])->middleware('role:admin,guru');
    Route::post('/events', [EventController::class, 'store'])->middleware('role:admin,guru');

    // Lapor kerusakan: admin dan guru (dari tombol pada daftar agenda)
    Route::middleware('role:admin,guru')->group(function () {
        Route::get('/complaints/create', [ComplaintController::class, 'create']);
        Route::post('/complaints', [ComplaintController::class, 'store']);
    });

    // Khusus Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin_dashboard');
        });
        Route::get('/admin/rooms', [RoomController::class, 'index']);
        Route::post('/admin/rooms', [RoomController::class, 'store']);

        Route::post('/admin/events/{event}/approve', [EventController::class, 'approve']);
        Route::get('/admin/events/{event}/reject', [EventController::class, 'rejectForm']);
        Route::post('/admin/events/{event}/reject', [EventController::class, 'reject']);
        Route::get('/admin/events/{event}/move', [EventController::class, 'moveForm']);
        Route::post('/admin/events/{event}/move', [EventController::class, 'moveRoom']);
        Route::get('/admin/events/{event}/edit', [EventController::class, 'edit']);
        Route::put('/admin/events/{event}', [EventController::class, 'update']);
        Route::delete('/admin/events/{event}', [EventController::class, 'destroy']);

        Route::get('/admin/reports', fn () => redirect('/admin/reports/daily'));
        Route::get('/admin/reports/daily', [ReportController::class, 'daily']);
        Route::get('/admin/reports/monthly', [ReportController::class, 'monthly']);

        Route::get('/admin/complaints', [ComplaintController::class, 'adminIndex']);
        Route::get('/admin/complaints/{complaint}/whatsapp', [ComplaintController::class, 'whatsappWarning']);
        Route::get('/admin/complaints/{complaint}/edit', [ComplaintController::class, 'edit']);
        Route::put('/admin/complaints/{complaint}', [ComplaintController::class, 'update']);
        Route::delete('/admin/complaints/{complaint}', [ComplaintController::class, 'destroy']);
    });

    // Khusus Guru/Organisasi
    Route::middleware('role:guru')->group(function () {
        Route::get('/guru/dashboard', function () {
            return view('guru_dashboard');
        });
    });

    // Khusus PIC Ruang
    Route::middleware('role:pic')->group(function () {
        Route::get('/pic/dashboard', [ComplaintController::class, 'picIndex']);
        Route::post('/pic/complaints/{complaint}/status', [ComplaintController::class, 'updateStatus']);
    });
});