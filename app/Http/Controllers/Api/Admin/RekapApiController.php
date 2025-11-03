<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rekap;
use App\Models\Siswa;
use Carbon\Carbon;

class RekapApiController extends Controller
{
    /**
     * Get all rekap data for admin
     */
    public function index(Request $request)
    {
        $query = Rekap::with('siswa.kelas');
        
        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal', [
                $request->start_date,
                $request->end_date
            ]);
        }
        
        // Filter by siswa
        if ($request->has('siswa_id')) {
            $query->where('siswa_id', $request->siswa_id);
        }
        
        // Filter by kelas
        if ($request->has('kelas_id')) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        $rekap = $query->orderBy('tanggal', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->get();

        return response()->json([
            'status' => 'success',
            'data' => $rekap
        ]);
    }

    /**
     * Get siswa yang terlambat hari ini
     */
    public function terlambatHariIni()
    {
        $today = Carbon::today()->toDateString();
        
        $terlambat = Rekap::with('siswa.kelas')
            ->where('tanggal', $today)
            ->where('status', 'Hadir')
            ->where('jam_masuk', '>', '07:00:00') // Contoh: telat jika masuk setelah jam 7
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $terlambat
        ]);
    }

    /**
     * Get rekap statistics
     */
    public function statistik(Request $request)
    {
        $start = $request->query('start', Carbon::now()->startOfMonth()->toDateString());
        $end = $request->query('end', Carbon::now()->endOfMonth()->toDateString());

        $total = Rekap::whereBetween('tanggal', [$start, $end])->count();
        $hadir = Rekap::whereBetween('tanggal', [$start, $end])->where('status', 'Hadir')->count();
        $izin = Rekap::whereBetween('tanggal', [$start, $end])->where('status', 'Izin')->count();
        $alpha = Rekap::whereBetween('tanggal', [$start, $end])->where('status', 'Alpha')->count();

        return response()->json([
            'periode' => "$start - $end",
            'statistik' => [
                'total' => $total,
                'hadir' => $hadir,
                'izin' => $izin,
                'alpha' => $alpha,
                'persentase_hadir' => $total > 0 ? round(($hadir / $total) * 100, 2) : 0,
            ]
        ]);
    }
}