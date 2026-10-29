<?php

namespace App\Services;

use App\Enums\StatusPresensi;
use App\Models\Rekap;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;

/**
 * Agregasi rekap presensi. Setiap metode memakai satu query GROUP BY,
 * bukan satu query per status/hari seperti sebelumnya.
 */
class StatistikService
{
    /**
     * Jumlah rekap per status, lengkap dengan status yang bernilai 0.
     *
     * @return array<string, int> status => jumlah
     */
    public function perStatus(Builder $query): array
    {
        $counts = $query->toBase()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(StatusPresensi::cases())
            ->mapWithKeys(fn (StatusPresensi $s) => [$s->value => (int) ($counts[$s->value] ?? 0)])
            ->all();
    }

    /** @return array<string, int> */
    public function harian(CarbonInterface $tanggal): array
    {
        return $this->perStatus(Rekap::where('tanggal', $tanggal->toDateString()));
    }

    /**
     * Tren kehadiran harian dalam rentang tanggal.
     *
     * @return list<array{tanggal: CarbonInterface, hadir: int, terlambat: int, izin: int, alpa: int}>
     */
    public function tren(CarbonInterface $dari, CarbonInterface $sampai): array
    {
        $rows = Rekap::query()->toBase()
            ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->selectRaw('tanggal, status, COUNT(*) as total')
            ->groupBy('tanggal', 'status')
            ->get()
            ->groupBy('tanggal');

        return collect(CarbonPeriod::create($dari, $sampai))->map(function (CarbonInterface $tgl) use ($rows) {
            $hari = $rows->get($tgl->toDateString(), collect())->pluck('total', 'status');

            return [
                'tanggal' => $tgl,
                'hadir' => (int) ($hari['hadir'] ?? 0),
                'terlambat' => (int) ($hari['terlambat'] ?? 0),
                'izin' => (int) (($hari['izin'] ?? 0) + ($hari['sakit'] ?? 0)),
                'alpa' => (int) ($hari['alpa'] ?? 0),
            ];
        })->values()->all();
    }

    /** Persentase kehadiran (hadir + terlambat) dari total rekap. */
    public function persentaseHadir(array $perStatus): int
    {
        $total = array_sum($perStatus);

        if ($total === 0) {
            return 0;
        }

        $hadir = ($perStatus[StatusPresensi::Hadir->value] ?? 0) + ($perStatus[StatusPresensi::Terlambat->value] ?? 0);

        return (int) round($hadir / $total * 100);
    }
}
