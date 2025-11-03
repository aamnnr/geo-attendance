<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Siswa extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'siswas';
    protected $primaryKey = 'siswa_id';
    
    /**
     * PERBARUI: Tambahkan kolom baru di sini
     */
    protected $fillable = [
        'siswa_nama',
        'siswa_nis',
        'kelas_id',
        'siswa_password',
        'siswa_email', // <-- BARU
        'siswa_hp',    // <-- BARU
        'siswa_alamat', // <-- BARU
    ];

    protected $hidden = [
        'siswa_password',
    ];

    public function getAuthPassword()
    {
        return $this->siswa_password;
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }

    public function rekap()
    {
        return $this->hasMany(Rekap::class, 'siswa_id', 'siswa_id');
    }

    public function izin()
    {
        return $this->hasMany(Izin::class, 'siswa_id', 'siswa_id');
    }
}

