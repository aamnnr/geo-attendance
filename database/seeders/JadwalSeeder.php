<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal; // Pastikan Anda memiliki model Jadwal

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jadwal::truncate();

        // Jadwal untuk Kelas 1 (10A - RPL)
        Jadwal::create([
            'kelas_id' => 1,
            'mapel' => 'Pemrograman Web',
            'hari' => 'Senin',
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '09:00:00',
        ]);
        Jadwal::create([
            'kelas_id' => 1,
            'mapel' => 'Basis Data',
            'hari' => 'Selasa',
            'jam_mulai' => '09:30:00',
            'jam_selesai' => '11:30:00',
        ]);

        // Jadwal untuk Kelas 2 (10B - TKJ)
        Jadwal::create([
            'kelas_id' => 2,
            'mapel' => 'Jaringan Dasar',
            'hari' => 'Senin',
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '09:00:00',
        ]);
    }
}
