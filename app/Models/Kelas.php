<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $primaryKey = 'kelas_id';
    
    protected $fillable = [
        'kelas_nama',
    ];

    /**
     * Relationship dengan Siswa
     */
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas_id', 'kelas_id');
    }

    /**
     * Relationship dengan Jadwal
     */
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'kelas_id', 'kelas_id');
    }

    /**
     * Relationship dengan Rekap melalui Siswa
     */
    public function rekap()
    {
        return $this->hasManyThrough(Rekap::class, Siswa::class, 'kelas_id', 'siswa_id', 'kelas_id', 'siswa_id');
    }
}