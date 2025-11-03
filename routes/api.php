<?php

use Illuminate\Support\Facades\Route;

// ====================== ADMIN API ======================
use App\Http\Controllers\Api\Admin\{
    AuthApiController as AdminAuthApiController,
    SiswaApiController as AdminSiswaApiController,
    KelasApiController as AdminKelasApiController,
    JadwalApiController as AdminJadwalApiController,
    RekapApiController as AdminRekapApiController,
    IzinApiController as AdminIzinApiController,
    LogApiController as AdminLogApiController
};

// ====================== SISWA API ======================
use App\Http\Controllers\Api\Siswa\{
    AuthApiController as SiswaAuthApiController,
    KelasApiController as SiswaKelasApiController,
    JadwalApiController as SiswaJadwalApiController,
    RekapApiController as SiswaRekapApiController,
    IzinApiController as SiswaIzinApiController,
    PresensiApiController as SiswaPresensiApiController,
    ProfileApiController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// =======================================================
//                  ROUTE ADMIN
// =======================================================
// Nama rute API Admin diawali dengan 'api.admin.'
Route::prefix('admin')->name('api.admin.')->group(function () {
    
    // Rute Publik (Tidak perlu login)
    Route::post('/login', [AdminAuthApiController::class, 'login'])->name('login');

    // Rute yang Dilindungi (Perlu token 'auth:sanctum')
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AdminAuthApiController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminRekapApiController::class, 'index'])->name('dashboard');
        
        Route::apiResource('siswa', AdminSiswaApiController::class);
        Route::apiResource('kelas', AdminKelasApiController::class);
        Route::apiResource('jadwal', AdminJadwalApiController::class);
        Route::apiResource('rekap', AdminRekapApiController::class);
        Route::apiResource('izin', AdminIzinApiController::class);
        Route::apiResource('log', AdminLogApiController::class);
    });
});

// =======================================================
//                  ROUTE SISWA
// =======================================================
// Nama rute API Siswa diawali dengan 'api.siswa.'
Route::prefix('siswa')->name('api.siswa.')->group(function () {
    
    // Rute Publik (Tidak perlu login)
    Route::post('/login', [SiswaAuthApiController::class, 'login'])->name('api.login');

    // Rute yang Dilindungi (Perlu token 'auth:sanctum')
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [ProfileApiController::class, 'me'])->name('profile');
        
        Route::get('/kelas', [SiswaKelasApiController::class, 'index'])->name('kelas.index');
        Route::get('/jadwal', [SiswaJadwalApiController::class, 'index'])->name('jadwal.index');
        Route::get('/rekap', [SiswaRekapApiController::class, 'index'])->name('rekap.index');

        Route::get('/izin', [SiswaIzinApiController::class, 'index'])->name('izin.index');
        Route::post('/izin', [SiswaIzinApiController::class, 'store'])->name('izin.store');
        

        Route::get('/presensi', [SiswaPresensiApiController::class, 'index'])->name('presensi.index');
    });
});

