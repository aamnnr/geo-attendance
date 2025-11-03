<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rekap;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Izin;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        
        // Statistik Hari Ini
        $totalHadir = Rekap::where('tanggal', $today)
                          ->where('status', 'Hadir')
                          ->count();
        
        $totalIzin = Rekap::where('tanggal', $today)
                         ->where('status', 'Izin')
                         ->count();
        
        $totalAlpha = Rekap::where('tanggal', $today)
                          ->where('status', 'Alpha')
                          ->count();
        
        // Total Data
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $izinPending = Izin::where('status', 'pending')->count();
        
        // Statistik Mingguan
        $statistikMingguan = $this->getStatistikMingguan();
        
        // Presensi Terbaru
        $presensiTerbaru = Rekap::with('siswa.kelas')
                               ->where('tanggal', $today)
                               ->where('status', 'Hadir')
                               ->orderBy('jam_masuk', 'desc')
                               ->limit(10)
                               ->get();

        $admin = session('admin');

        return view('admin.dashboard', compact(
            'totalHadir',
            'totalIzin',
            'totalAlpha', 
            'totalSiswa',
            'totalKelas',
            'izinPending',
            'statistikMingguan',
            'presensiTerbaru',
            'admin',
            'today'
        ));
    }

    private function getStatistikMingguan()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $statistik = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $hari = $date->translatedFormat('l');
            
            $hadir = Rekap::where('tanggal', $date->toDateString())
                         ->where('status', 'Hadir')
                         ->count();

            $statistik[$hari] = $hadir;
        }

        return $statistik;
    }
}