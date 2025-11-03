<?php

use Illuminate\Support\Facades\Route;

// ==================== ADMIN CONTROLLERS ====================
use App\Http\Controllers\Web\Admin\{
    AdminAuthController,
    AdminDashboardController,
    AdminKelasController,
    AdminJadwalController,
    AdminRekapController,
    AdminIzinController,
    AdminSiswaController,
    AdminSettingController
};

// ==================== SISWA CONTROLLERS ====================
use App\Http\Controllers\Web\Siswa\{
    SiswaAuthController,
    SiswaDashboardController,
    SiswaRekapController,
    SiswaPresensiController,
    SiswaIzinController,
    SiswaProfileController,
    SiswaJadwalController
};

// ==================== LANDING PAGE ====================
Route::get('/', fn() => view('welcome'))->name('home');


// ==================== ADMIN AUTH ====================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // ==================== ADMIN PANEL ====================
    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('/kelas', AdminKelasController::class);
        Route::resource('/jadwal', AdminJadwalController::class);
        Route::resource('/rekap', AdminRekapController::class);
        Route::resource('/izin', AdminIzinController::class);
        Route::post('/izin/{izin}/approve', [AdminIzinController::class, 'approve'])->name('izin.approve');
        Route::post('/izin/{izin}/reject', [AdminIzinController::class, 'reject'])->name('izin.reject');
        Route::resource('/siswa', AdminSiswaController::class);
        Route::resource('/rekap', AdminRekapController::class);
        Route::get('/rekap/export/excel', [AdminRekapController::class, 'exportExcel'])->name('rekap.export.excel');
        Route::get('/rekap/export/pdf', [AdminRekapController::class, 'exportPdf'])->name('rekap.export.pdf');
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });
});


// ==================== SISWA AUTH ====================
Route::prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/login', [SiswaAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [SiswaAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [SiswaAuthController::class, 'logout'])->name('logout');

    // ==================== SISWA PANEL ====================
    Route::middleware('siswa.auth')->group(function () {
        Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
        Route::get('/rekap', [SiswaRekapController::class, 'index'])->name('rekap');
        Route::get('/presensi', [SiswaPresensiController::class, 'index'])->name('presensi');
        Route::post('/presensi', [SiswaPresensiController::class, 'store'])->name('presensi.store');
        Route::get('/izin', [SiswaIzinController::class, 'index'])->name('izin');
        Route::post('/izin', [SiswaIzinController::class, 'store'])->name('izin.store');
        Route::delete('/izin/{izin}', [SiswaIzinController::class, 'destroy'])->name('izin.destroy');
        Route::get('/profile', [SiswaProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [SiswaProfileController::class, 'update'])->name('profile.update');
        Route::get('/jadwal', [SiswaJadwalController::class, 'index'])->name('jadwal');
    });
});
