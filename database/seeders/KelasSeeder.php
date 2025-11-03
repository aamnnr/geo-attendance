<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas; // Pastikan Anda memiliki model Kelas

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kelas::truncate();

        // Buat beberapa contoh kelas
        Kelas::create(['kelas_nama' => '10A - RPL']); // Akan mendapat kelas_id = 1
        Kelas::create(['kelas_nama' => '10B - TKJ']); // Akan mendapat kelas_id = 2
        Kelas::create(['kelas_nama' => '11A - RPL']); // Akan mendapat kelas_id = 3
        Kelas::create(['kelas_nama' => '11B - TKJ']); // Akan mendapat kelas_id = 4
        Kelas::create(['kelas_nama' => '12A - RPL']); // Akan mendapat kelas_id = 5
        Kelas::create(['kelas_nama' => '12B - TKJ']); // Akan mendapat kelas_id = 6
    }
}
