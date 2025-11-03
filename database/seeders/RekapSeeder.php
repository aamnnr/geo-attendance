<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rekap; // Pastikan Anda memiliki model Rekap
use Carbon\Carbon;

class RekapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rekap::truncate();

        // Rekap untuk Siswa 1 (Budi) - Kemarin
        Rekap::create([
            'siswa_id' => 1,
            'tanggal' => Carbon::today()->subDays(1),
            'jam_masuk' => '06:55:00',
            'jam_keluar' => '14:05:00',
            'status' => 'hadir',
            'latitude' => -7.70, // Lokasi contoh
            'longitude' => 110.60,
        ]);

        // Rekap untuk Siswa 1 (Budi) - 2 Hari lalu (Alpa)
        Rekap::create([
            'siswa_id' => 1,
            'tanggal' => Carbon::today()->subDays(2),
            'status' => 'alpa',
        ]);

        // Rekap untuk Siswa 2 (Citra) - Kemarin (Terlambat)
        Rekap::create([
            'siswa_id' => 2,
            'tanggal' => Carbon::today()->subDays(1),
            'jam_masuk' => '07:15:00',
            'jam_keluar' => '14:00:00',
            'status' => 'terlambat',
            'latitude' => -7.71,
            'longitude' => 110.61,
        ]);
    }
}
