<?php

namespace App\Console\Commands;

use App\Services\AlpaService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MarkAlpaCommand extends Command
{
    protected $signature = 'rekap:alpa {tanggal? : Tanggal (Y-m-d), default hari ini}';

    protected $description = 'Menandai siswa yang tidak presensi dan tidak izin sebagai Alpa';

    public function handle(AlpaService $alpa): int
    {
        $tanggal = Carbon::parse($this->argument('tanggal') ?? today());
        $jumlah = $alpa->tandai($tanggal);

        $this->components->info("{$jumlah} siswa ditandai Alpa untuk {$tanggal->toDateString()}.");

        return self::SUCCESS;
    }
}
