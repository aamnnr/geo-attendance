<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin; // Pastikan Anda memiliki model Admin
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada, untuk menghindari error unique
        Admin::truncate();

        // Buat admin default
        Admin::create([
            'admin_username' => 'admin',
            'admin_password' => Hash::make('password'), // Ganti 'password' dengan password aman
            'admin_nama' => 'Administrator Utama',
        ]);
    }
}
