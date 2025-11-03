<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema; // <-- 1. IMPORT SCHEMA

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Ini akan memanggil seeder dalam urutan yang benar
     * untuk menghindari error foreign key.
     */
    public function run(): void
    {
        // 2. NONAKTIFKAN FOREIGN KEY CHECKS
        Schema::disableForeignKeyConstraints();

        $this->call([
            AdminSeeder::class,
            KelasSeeder::class,
            SiswaSeeder::class, 
            JadwalSeeder::class, 
            RekapSeeder::class, 
            IzinSeeder::class, 
        ]);

        // 3. AKTIFKAN KEMBALI FOREIGN KEY CHECKS
        Schema::enableForeignKeyConstraints();
    }
}

