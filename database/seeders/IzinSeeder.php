<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Izin; // Pastikan Anda memiliki model Izin
use Carbon\Carbon;

class IzinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Izin::truncate();

        // Izin 'pending' untuk Siswa 1
        Izin::create([
            'siswa_id' => 1,
            'keterangan' => 'Sakit demam',
            'izin_dari' => Carbon::today(),
            'izin_sampai' => Carbon::today()->addDays(1),
            'status' => 'pending',
        ]);

        // Izin 'disetujui' untuk Siswa 2
        Izin::create([
            'siswa_id' => 2,
            'admin_id' => 1, // Disetujui oleh Admin 1
            'keterangan' => 'Acara keluarga',
            'izin_dari' => Carbon::today()->subDays(5),
            'izin_sampai' => Carbon::today()->subDays(5),
            'status' => 'disetujui',
        ]);

        // Izin 'ditolak' untuk Siswa 3
        Izin::create([
            'siswa_id' => 3,
            'admin_id' => 1, // Ditolak oleh Admin 1
            'keterangan' => 'Alasan tidak jelas',
            'izin_dari' => Carbon::today()->subDays(2),
            'izin_sampai' => Carbon::today()->subDays(2),
            'status' => 'ditolak',
        ]);
    }
}
