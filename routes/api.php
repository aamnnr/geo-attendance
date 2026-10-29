<?php

use App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Api\Siswa;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API (token Sanctum)
|--------------------------------------------------------------------------
|
| Login mengembalikan Bearer token dengan ability 'admin' atau 'siswa',
| sehingga token siswa tidak bisa dipakai untuk endpoint admin (dan sebaliknya).
|
*/

Route::prefix('admin')->name('api.admin.')->group(function () {
    Route::post('login', [Admin\AuthController::class, 'login'])->middleware('throttle:10,1')->name('login');

    Route::middleware(['auth:sanctum', 'abilities:admin'])->group(function () {
        Route::get('me', [Admin\AuthController::class, 'me'])->name('me');
        Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', Admin\DashboardController::class)->name('dashboard');

        Route::apiResource('siswa', Admin\SiswaController::class);
        Route::apiResource('kelas', Admin\KelasController::class)->parameters(['kelas' => 'kelas']);
        Route::apiResource('jadwal', Admin\JadwalController::class);
        Route::apiResource('rekap', Admin\RekapController::class);

        Route::get('izin', [Admin\IzinController::class, 'index'])->name('izin.index');
        Route::post('izin/{izin}/approve', [Admin\IzinController::class, 'approve'])->name('izin.approve');
        Route::post('izin/{izin}/reject', [Admin\IzinController::class, 'reject'])->name('izin.reject');

        Route::get('log', Admin\ActivityLogController::class)->name('log');
    });
});

Route::prefix('siswa')->name('api.siswa.')->group(function () {
    Route::post('login', [Siswa\AuthController::class, 'login'])->middleware('throttle:10,1')->name('login');

    Route::middleware(['auth:sanctum', 'abilities:siswa'])->group(function () {
        Route::get('me', [Siswa\AuthController::class, 'me'])->name('me');
        Route::post('logout', [Siswa\AuthController::class, 'logout'])->name('logout');
        Route::put('profil', [Siswa\ProfileController::class, 'update'])->name('profile.update');

        Route::get('presensi', [Siswa\PresensiController::class, 'show'])->name('presensi.show');
        Route::post('presensi', [Siswa\PresensiController::class, 'store'])->middleware('throttle:10,1')->name('presensi.store');

        Route::get('rekap', Siswa\RekapController::class)->name('rekap');
        Route::get('jadwal', Siswa\JadwalController::class)->name('jadwal');

        Route::get('izin', [Siswa\IzinController::class, 'index'])->name('izin.index');
        Route::post('izin', [Siswa\IzinController::class, 'store'])->name('izin.store');
        Route::delete('izin/{izin}', [Siswa\IzinController::class, 'destroy'])->name('izin.destroy');
    });
});
