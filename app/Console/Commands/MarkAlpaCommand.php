<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\Rekap;
use App\Models\Izin;
use Carbon\Carbon;
use App\Models\Log;

class MarkAlpaCommand extends Command
{
    protected $signature = 'rekap:alpa';
    protected $description = 'Menandai otomatis siswa yang tidak presensi dan tidak izin sebagai Alpa';

    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $siswas = Siswa::all();
        $count = 0;

        foreach ($siswas as $siswa) {
            $adaPresensi = Rekap::where('siswa_id', $siswa->siswa_id)
                ->whereDate('tanggal', $today)
                ->exists();

            $sedangIzin = Izin::where('siswa_id', $siswa->siswa_id)
                ->whereDate('izin_dari', '<=', $today)
                ->whereDate('izin_sampai', '>=', $today)
                ->exists();

            if (!$adaPresensi && !$sedangIzin) {
                Rekap::create([
                    'siswa_id' => $siswa->siswa_id,
                    'tanggal' => $today,
                    'status' => 'Alpa',
                    'jam_masuk' => null,
                    'jam_keluar' => null,
                ]);
                $count++;
            }
        }

        $this->info("✅ {$count} siswa ditandai Alpa otomatis untuk {$today}");
        Log::create([
        'tipe' => 'alpa_otomatis',
        'pesan' => "{$count} siswa ditandai Alpa otomatis untuk {$today}",
    ]);

        $this->info("✅ {$count} siswa ditandai Alpa otomatis untuk {$today}");

    }
}
