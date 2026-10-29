<?php

namespace App\Services;

use App\Enums\Hari;
use App\Enums\StatusIzin;
use App\Enums\StatusPresensi;
use App\Models\ActivityLog;
use App\Models\Jadwal;
use App\Models\Libur;
use App\Models\Rekap;
use App\Models\Siswa;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

class AlpaService
{
    /**
     * Menandai siswa yang tidak presensi & tidak izin pada tanggal tertentu sebagai Alpa.
     * Hanya berlaku untuk kelas yang punya jadwal di hari itu dan bukan hari libur.
     *
     * @return int jumlah siswa yang ditandai
     */
    public function tandai(CarbonInterface $tanggal): int
    {
        $tgl = $tanggal->toDateString();

        if (Libur::untukTanggal($tgl)) {
            return 0;
        }

        $kelasIds = Jadwal::where('hari', Hari::fromDate($tanggal))->distinct()->pluck('kelas_id');

        $siswaIds = Siswa::whereIn('kelas_id', $kelasIds)
            ->whereDoesntHave('rekap', fn (Builder $q) => $q->where('tanggal', $tgl))
            ->whereDoesntHave('izin', fn (Builder $q) => $q->where('status', StatusIzin::Disetujui)->beririsan($tgl))
            ->pluck('siswa_id');

        $now = now();

        Rekap::insert($siswaIds->map(fn ($id) => [
            'siswa_id' => $id,
            'tanggal' => $tgl,
            'status' => StatusPresensi::Alpa->value,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all());

        ActivityLog::record('alpa_otomatis', "{$siswaIds->count()} siswa ditandai Alpa otomatis untuk {$tgl}.");

        return $siswaIds->count();
    }
}
