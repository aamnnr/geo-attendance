<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\StatusIzin;
use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Services\StatistikService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(StatistikService $statistik): JsonResponse
    {
        $hariIni = $statistik->harian(today());

        return response()->json(['data' => [
            'tanggal' => today()->toDateString(),
            'hari_ini' => $hariIni,
            'persentase_hadir' => $statistik->persentaseHadir($hariIni),
            'total_siswa' => Siswa::count(),
            'total_kelas' => Kelas::count(),
            'izin_pending' => Izin::status(StatusIzin::Pending)->count(),
            'tren_7_hari' => collect($statistik->tren(today()->subDays(6), today()))
                ->map(fn (array $row) => ['tanggal' => $row['tanggal']->toDateString()] + $row),
        ]]);
    }
}
