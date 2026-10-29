<?php

namespace App\Models;

use App\Enums\Hari;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    use HasFactory;

    protected $primaryKey = 'jadwal_id';

    protected $fillable = [
        'kelas_id',
        'mapel',
        'guru',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    protected function casts(): array
    {
        return [
            'hari' => Hari::class,
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }

    public function scopeUntukHari(Builder $query, int $kelasId, Hari $hari): Builder
    {
        return $query->where('kelas_id', $kelasId)->where('hari', $hari)->orderBy('jam_mulai');
    }

    /** Jam dalam format HH:MM. */
    public function jamMulai(): string
    {
        return substr($this->jam_mulai, 0, 5);
    }

    public function jamSelesai(): string
    {
        return substr($this->jam_selesai, 0, 5);
    }
}
