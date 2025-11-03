<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa; // Pastikan Anda memiliki model Siswa
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Siswa::truncate();

        // Buat siswa untuk kelas 1 (10A - RPL)
        Siswa::create([
            'siswa_nama' => 'Budi Santoso',
            'siswa_nis' => '22001',
            'kelas_id' => 1,
            'siswa_password' => Hash::make('123456'), // Kolom password yang benar
            'siswa_email' => 'budi@example.com',
            'siswa_hp' => '081234567890',
            'siswa_alamat' => 'Jl. Merdeka No. 1, Klaten',
        ]);

        // Buat siswa untuk kelas 2 (10B - TKJ)
        Siswa::create([
            'siswa_nama' => 'Citra Lestari',
            'siswa_nis' => '22002',
            'kelas_id' => 2,
            'siswa_password' => Hash::make('123456'),
            'siswa_email' => 'citra@example.com',
            'siswa_hp' => '081234567891',
            'siswa_alamat' => 'Jl. Pahlawan No. 2, Klaten',
        ]);
        
        // Buat siswa untuk kelas 1 (10A - RPL) lagi
        Siswa::create([
            'siswa_nama' => 'Agus Setiawan',
            'siswa_nis' => '22003',
            'kelas_id' => 1,
            'siswa_password' => Hash::make('123456'),
            'siswa_email' => 'agus@example.com',
            'siswa_hp' => '081234567892',
            'siswa_alamat' => 'Jl. Kenanga No. 3, Klaten',
        ]);
    }
}

