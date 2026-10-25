<?php

use App\Http\Controllers\AttendanceRecordController;
use App\Http\Controllers\AttendanceSessionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClassScheduleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Teacher Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

    // Third Party OAuth Social Authentication
    Route::get('/auth/telegram/callback', [SocialAuthController::class, 'telegramCallback'])->name('social.telegram.callback');
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Public Student Attendance View Route (No login required, rate-limited)
Route::get('/attendance/class/{token}', [\App\Http\Controllers\PublicAttendanceController::class, 'view'])
    ->name('public.class.attendance')
    ->middleware('throttle:60,1');

Route::get('/attendance/session/{token}', [\App\Http\Controllers\PublicAttendanceController::class, 'viewSession'])
    ->name('public.session.attendance')
    ->middleware('throttle:60,1');

/*
|--------------------------------------------------------------------------
| Authenticated Teacher Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Class Share Link
    Route::post('/classes/{class}/share-link', [ClassController::class, 'regenerateShareLink'])->name('classes.share-link.regenerate');

    // Profile & Password Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Classes Management
    Route::resource('classes', ClassController::class);

    // Class Schedules
    Route::post('/classes/{class}/schedules', [ClassScheduleController::class, 'store'])->name('classes.schedules.store');
    Route::delete('/classes/{class}/schedules/{schedule}', [ClassScheduleController::class, 'destroy'])->name('classes.schedules.destroy');

    // Students Directory & CSV Import
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/import', [StudentController::class, 'importCsvForm'])->name('students.import');
    Route::get('/students/import/excel-template', [StudentController::class, 'downloadExcelTemplate'])->name('students.import.excel-template');
    Route::get('/students/import/template', [StudentController::class, 'downloadCsvTemplate'])->name('students.import.template');
    Route::post('/students/import', [StudentController::class, 'processCsvImport'])->name('students.import.process');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    // Attendance Sessions & Records
    Route::get('/sessions', [AttendanceSessionController::class, 'index'])->name('sessions.index');
    Route::get('/sessions/create', [AttendanceSessionController::class, 'create'])->name('sessions.create');
    Route::post('/sessions', [AttendanceSessionController::class, 'store'])->name('sessions.store');
    Route::post('/sessions/{session}/share-link', [AttendanceSessionController::class, 'regenerateShareLink'])->name('sessions.share-link.regenerate');
    Route::get('/sessions/{session}', [AttendanceSessionController::class, 'show'])->name('sessions.show');
    Route::put('/sessions/{session}/records', [AttendanceSessionController::class, 'updateRecords'])->name('sessions.records.update');
    Route::delete('/sessions/{session}', [AttendanceSessionController::class, 'destroy'])->name('sessions.destroy');

    // Attendance Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/classes/{class}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/classes/{class}/export', [ReportController::class, 'exportCsv'])->name('reports.export');
    Route::get('/reports/classes/{class}/print', [ReportController::class, 'print'])->name('reports.print');
});
