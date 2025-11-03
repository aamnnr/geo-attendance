<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekap extends Model
{
    use HasFactory;

    // Tentukan primary key jika bukan 'id'
    protected $primaryKey = 'rekap_id';

    // Izinkan mass assignment untuk kolom-kolom ini
    // INI ADALAH PERBAIKAN PENTING
    protected $fillable = [
        'siswa_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'latitude',
        'longitude',
    ];

    /**
     * Relasi ke Siswa
     */
    public function siswa()
    {
        // asumsikan foreign key adalah 'siswa_id' dan primary key siswa adalah 'siswa_id'
        return $this->belongsTo(Siswa::class, 'siswa_id', 'siswa_id');
    }
}

