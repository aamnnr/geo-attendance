<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $primaryKey = 'kelas_id';

    protected $fillable = [
        'kelas_nama',
        'wali_kelas',
    ];

    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kelas_id', 'kelas_id');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'kelas_id', 'kelas_id');
    }

    public function rekap(): HasManyThrough
    {
        return $this->hasManyThrough(Rekap::class, Siswa::class, 'kelas_id', 'siswa_id', 'kelas_id', 'siswa_id');
    }
}
