<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    // Sesuai dengan SQL dump Anda, primary key-nya BUKAN 'id'
    protected $primaryKey = 'jadwal_id';

    // Sesuai dengan SQL dump Anda
    protected $fillable = [
        'kelas_id',
        'mapel',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    /**
     * Relasi ke model Kelas
     */
    public function kelas()
    {
        // Pastikan nama model Kelas Anda benar
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }
}
