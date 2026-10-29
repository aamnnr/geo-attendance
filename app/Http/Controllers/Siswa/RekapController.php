<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Services\StatistikService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class RekapController extends Controller
{
    public function __invoke(Request $request, StatistikService $statistik): View
    {
        $siswa = $request->user('siswa');

        // Filter bulan dalam format YYYY-MM, default bulan ini.
        $bulan = rescue(
            fn () => Carbon::createFromFormat('!Y-m', (string) $request->query('bulan', now()->format('Y-m'))),
            now()->startOfMonth(),
            report: false,
        );
        $rentang = [$bulan->toDateString(), $bulan->copy()->endOfMonth()->toDateString()];

        return view('siswa.rekap', [
            'bulan' => $bulan,
            'ringkasan' => $statistik->perStatus($siswa->rekap()->getQuery()->whereBetween('tanggal', $rentang)),
            'rekaps' => $siswa->rekap()
                ->whereBetween('tanggal', $rentang)
                ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
                ->latest('tanggal')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }
}
