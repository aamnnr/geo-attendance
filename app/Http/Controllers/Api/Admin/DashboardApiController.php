<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rekap;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Izin;
use Carbon\Carbon;

class DashboardApiController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();
        
        // Statistik hari ini
        $presensiHariIni = Rekap::where('tanggal', $today)->count();
        $hadirHariIni = Rekap::where('tanggal', $today)->where('status', 'Hadir')->count();
        $terlambatHariIni = Rekap::where('tanggal', $today)
                                ->where('status', 'Hadir')
                                ->where('jam_masuk', '>', '07:00:00')
                                ->count();
        $izinHariIni = Rekap::where('tanggal', $today)->where('status', 'Izin')->count();
        
        // Statistik bulan ini
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
        
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $izinPending = Izin::where('status', 'pending')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'hari_ini' => [
                    'presensi' => $presensiHariIni,
                    'hadir' => $hadirHariIni,
                    'terlambat' => $terlambatHariIni,
                    'izin' => $izinHariIni,
                ],
                'total' => [
                    'siswa' => $totalSiswa,
                    'kelas' => $totalKelas,
                    'izin_pending' => $izinPending,
                ],
                'periode' => [
                    'bulan' => Carbon::now()->translatedFormat('F Y'),
                    'tanggal' => Carbon::now()->translatedFormat('d F Y'),
                ]
            ]
        ]);
    }

    /**
     * Get rekap harian
     */
    public function rekapHarian(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());
        
        $rekap = Rekap::with('siswa.kelas')
            ->where('tanggal', $date)
            ->orderBy('jam_masuk')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $rekap,
            'tanggal' => $date
        ]);
    }

    /**
     * Get statistik mingguan
     */
    public function statistikMingguan()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $statistik = [];
        
        for ($date = $startOfWeek; $date->lte($endOfWeek); $date->addDay()) {
            $tanggal = $date->toDateString();
            
            $hadir = Rekap::where('tanggal', $tanggal)->where('status', 'Hadir')->count();
            $izin = Rekap::where('tanggal', $tanggal)->where('status', 'Izin')->count();
            $alpha = Rekap::where('tanggal', $tanggal)->where('status', 'Alpha')->count();
            
            $statistik[] = [
                'hari' => $date->translatedFormat('l'),
                'tanggal' => $tanggal,
                'hadir' => $hadir,
                'izin' => $izin,
                'alpha' => $alpha,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $statistik,
            'periode' => $startOfWeek->translatedFormat('d M') . ' - ' . $endOfWeek->translatedFormat('d M Y')
        ]);
    }
}