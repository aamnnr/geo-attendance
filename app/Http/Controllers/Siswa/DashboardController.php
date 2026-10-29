<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Services\PresensiService;
use App\Services\StatistikService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, PresensiService $presensi, StatistikService $statistik): View
    {
        $siswa = $request->user('siswa')->load('kelas');

        $bulanIni = $statistik->perStatus($siswa->rekap()->getQuery()->whereBetween('tanggal', [
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString(),
        ]));

        return view('siswa.dashboard', [
            'siswa' => $siswa,
            'kondisi' => $presensi->kondisiHariIni($siswa),
            'bulanIni' => $bulanIni,
            'persentaseHadir' => $statistik->persentaseHadir($bulanIni),
            'riwayat' => $siswa->rekap()->latest('tanggal')->limit(5)->get(),
        ]);
    }
}
