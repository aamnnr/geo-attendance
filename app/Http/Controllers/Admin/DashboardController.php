<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusIzin;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Izin;
use App\Models\Kelas;
use App\Models\Rekap;
use App\Models\Siswa;
use App\Services\StatistikService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(StatistikService $statistik): View
    {
        $today = today();
        $hariIni = $statistik->harian($today);
        $totalSiswa = Siswa::count();

        return view('admin.dashboard', [
            'hariIni' => $hariIni,
            'totalSiswa' => $totalSiswa,
            'totalKelas' => Kelas::count(),
            'belumPresensi' => max(0, $totalSiswa - array_sum($hariIni)),
            'persentaseHadir' => $statistik->persentaseHadir($hariIni),
            'tren' => $statistik->tren($today->copy()->subDays(6), $today),
            'izinPending' => Izin::with('siswa.kelas')->status(StatusIzin::Pending)->latest()->limit(5)->get(),
            'jumlahIzinPending' => Izin::status(StatusIzin::Pending)->count(),
            'presensiTerbaru' => Rekap::with('siswa.kelas')
                ->where('tanggal', $today->toDateString())
                ->whereNotNull('jam_masuk')
                ->latest('updated_at')
                ->limit(8)
                ->get(),
            'aktivitas' => ActivityLog::latest()->limit(5)->get(),
        ]);
    }
}
