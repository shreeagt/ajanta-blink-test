<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DoctorPosterAdminController;
use App\Http\Controllers\DoctorPosterController;
use App\Http\Controllers\PrescriptionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ─── FRONTEND: Blink Test ──────────────────────────────────
Route::get('/', function () {
    return view('blink_test_app', [
        'all_translations' => \App\Models\AppTranslation::getAll(),
        'all_symptoms' => \App\Models\CvsSymptom::getAllGrouped()
    ]);
})->name('blink.app');

// Blink Test routes
Route::post('/save-blink-test', [\App\Http\Controllers\BlinkTestController::class, 'store'])->name('blink_test.save');
Route::post('/save-cvs-test', [\App\Http\Controllers\BlinkTestController::class, 'storeCvs'])->name('cvs_test.save');
Route::get('/prescription-dashboard', [\App\Http\Controllers\BlinkTestController::class, 'dashboardStats'])->name('prescription.dashboard');
Route::get('/blink-test/{id}/detail', [\App\Http\Controllers\BlinkTestController::class, 'getTestDetail'])->name('blink_test.detail');
Route::post('/blink-login', [DoctorPosterController::class, 'login'])->name('blink.login');
Route::post('/set-language', [DoctorPosterController::class, 'setLanguage'])->name('blink.set_language');

// Doctor Management
Route::get('/doctors', [\App\Http\Controllers\DoctorController::class, 'index'])->name('doctors.index');
Route::post('/doctors', [\App\Http\Controllers\DoctorController::class, 'store'])->name('doctors.store');


Route::get('/{emp_code}', function ($emp_code) {
    // Check if employee exists to be safe
    $employee = \App\Models\Employee::where('emp_code', $emp_code)->first();
    if (!$employee && $emp_code !== 'admin') {
        return redirect('/');
    }
    return view('blink_test_app', [
        'emp_code' => $emp_code,
        'emp_name' => $employee ? $employee->name : null,
        'all_translations' => \App\Models\AppTranslation::getAll(),
        'all_symptoms' => \App\Models\CvsSymptom::getAllGrouped()
    ]);
});


// ─── ADMIN: Login ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::view('/admin/login', 'auth.adminlogin')->name('admin.login');
    Route::post('/admin/verify-admin', [AdminController::class, 'verifyAdmin'])->name('verify.admin');
});

// ─── ADMIN: Dashboard (requires auth + IsAdmin middleware) ────────────────────
Route::middleware(['auth', 'IsAdmin'])->prefix('admin')->group(function () {
    // Blink Test admin routes
    Route::get('/blink-dashboard', [DoctorPosterAdminController::class, 'dashboard'])->name('admin.blink.dashboard');
    Route::get('/manpower-master', [DoctorPosterAdminController::class, 'manpower'])->name('admin.manpower.master');
    Route::get('/import-manpower', [DoctorPosterAdminController::class, 'importEmployees'])->name('admin.import.manpower');

    // Admin utilities
    Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout.perform');
    Route::get('/change-password', [AdminController::class, 'showChangePasswordForm'])->name('admin.change_password');
    Route::post('/change-password', [AdminController::class, 'updatePassword'])->name('admin.change_password.update');
});
