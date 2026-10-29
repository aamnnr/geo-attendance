<?php

namespace App\Models;

use App\Casts\DateOnly;
use App\Enums\StatusPresensi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rekap extends Model
{
    use HasFactory;

    protected $primaryKey = 'rekap_id';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'latitude',
        'longitude',
        'latitude_keluar',
        'longitude_keluar',
        'jarak',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => DateOnly::class,
            'status' => StatusPresensi::class,
            'jarak' => 'integer',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'siswa_id');
    }

    /**
     * Filter bersama untuk halaman rekap, export, dan API.
     *
     * @param  array<string, mixed>  $filters  kelas_id, siswa_id, status, tanggal_mulai, tanggal_sampai, q
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['kelas_id'] ?? null, fn (Builder $q, $kelasId) => $q->whereHas('siswa', fn (Builder $s) => $s->where('kelas_id', $kelasId)))
            ->when($filters['siswa_id'] ?? null, fn (Builder $q, $siswaId) => $q->where('siswa_id', $siswaId))
            ->when($filters['status'] ?? null, fn (Builder $q, $status) => $q->where('status', $status))
            ->when($filters['tanggal_mulai'] ?? null, fn (Builder $q, $tgl) => $q->where('tanggal', '>=', $tgl))
            ->when($filters['tanggal_sampai'] ?? null, fn (Builder $q, $tgl) => $q->where('tanggal', '<=', $tgl))
            ->when($filters['q'] ?? null, fn (Builder $q, $term) => $q->whereHas('siswa', fn (Builder $s) => $s->search($term)));
    }

    public function jamMasuk(): ?string
    {
        return $this->jam_masuk ? substr($this->jam_masuk, 0, 5) : null;
    }

    public function jamKeluar(): ?string
    {
        return $this->jam_keluar ? substr($this->jam_keluar, 0, 5) : null;
    }
}
