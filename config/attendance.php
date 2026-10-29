<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pengaturan Default Presensi
    |--------------------------------------------------------------------------
    |
    | Nilai ini dipakai sampai admin menyimpan pengaturan di halaman
    | Pengaturan. Setelah itu nilai di tabel `settings` yang berlaku.
    |
    */

    'defaults' => [
        'school_name' => env('SCHOOL_NAME', 'SMK Negeri 1'),
        'school_lat' => env('SCHOOL_LAT', -7.7956),
        'school_lng' => env('SCHOOL_LNG', 110.3695),
        'school_radius' => env('SCHOOL_RADIUS', 100), // meter
        'toleransi_terlambat' => 15, // menit setelah jam pelajaran pertama
        'batas_presensi_masuk' => '12:00', // setelah jam ini presensi masuk ditutup
    ],

];
