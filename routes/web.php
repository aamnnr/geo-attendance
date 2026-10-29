<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Siswa;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

/*
|--------------------------------------------------------------------------
| Panel Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [Admin\AuthController::class, 'create'])->name('login');
        Route::post('login', [Admin\AuthController::class, 'store'])->name('login.store');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'destroy'])->name('logout');

        Route::redirect('/', '/admin/dashboard');
        Route::get('dashboard', Admin\DashboardController::class)->name('dashboard');

        Route::resource('siswa', Admin\SiswaController::class);
        Route::resource('kelas', Admin\KelasController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['kelas' => 'kelas']);
        Route::resource('jadwal', Admin\JadwalController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::get('rekap/export/excel', [Admin\RekapController::class, 'exportExcel'])->name('rekap.export.excel');
        Route::get('rekap/export/pdf', [Admin\RekapController::class, 'exportPdf'])->name('rekap.export.pdf');
        Route::resource('rekap', Admin\RekapController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::get('izin', [Admin\IzinController::class, 'index'])->name('izin.index');
        Route::post('izin', [Admin\IzinController::class, 'store'])->name('izin.store');
        Route::post('izin/{izin}/approve', [Admin\IzinController::class, 'approve'])->name('izin.approve');
        Route::post('izin/{izin}/reject', [Admin\IzinController::class, 'reject'])->name('izin.reject');

        Route::resource('libur', Admin\LiburController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::get('pengaturan', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('pengaturan', [Admin\SettingController::class, 'update'])->name('settings.update');

        Route::get('log', Admin\ActivityLogController::class)->name('log');
    });
});

/*
|--------------------------------------------------------------------------
| Portal Siswa
|--------------------------------------------------------------------------
*/
Route::prefix('siswa')->name('siswa.')->group(function () {
    Route::middleware('guest:siswa')->group(function () {
        Route::get('login', [Siswa\AuthController::class, 'create'])->name('login');
        Route::post('login', [Siswa\AuthController::class, 'store'])->name('login.store');
    });

    Route::middleware('auth:siswa')->group(function () {
        Route::post('logout', [Siswa\AuthController::class, 'destroy'])->name('logout');

        Route::redirect('/', '/siswa/dashboard');
        Route::get('dashboard', Siswa\DashboardController::class)->name('dashboard');

        Route::get('presensi', [Siswa\PresensiController::class, 'create'])->name('presensi');
        Route::post('presensi', [Siswa\PresensiController::class, 'store'])->name('presensi.store')->middleware('throttle:10,1');

        Route::get('rekap', Siswa\RekapController::class)->name('rekap');
        Route::get('jadwal', Siswa\JadwalController::class)->name('jadwal');

        Route::get('izin', [Siswa\IzinController::class, 'index'])->name('izin');
        Route::post('izin', [Siswa\IzinController::class, 'store'])->name('izin.store');
        Route::delete('izin/{izin}', [Siswa\IzinController::class, 'destroy'])->name('izin.destroy');

        Route::get('profil', [Siswa\ProfileController::class, 'edit'])->name('profile');
        Route::put('profil', [Siswa\ProfileController::class, 'update'])->name('profile.update');
    });
});
