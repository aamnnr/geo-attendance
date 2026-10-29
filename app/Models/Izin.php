<?php

namespace App\Models;

use App\Casts\DateOnly;
use App\Enums\JenisIzin;
use App\Enums\StatusIzin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Izin extends Model
{
    use HasFactory;

    protected $primaryKey = 'izin_id';

    protected $fillable = [
        'siswa_id',
        'admin_id',
        'jenis',
        'keterangan',
        'izin_dari',
        'izin_sampai',
        'bukti',
        'status',
        'catatan_admin',
        'diproses_pada',
    ];

    protected function casts(): array
    {
        return [
            'izin_dari' => DateOnly::class,
            'izin_sampai' => DateOnly::class,
            'diproses_pada' => 'datetime',
            'jenis' => JenisIzin::class,
            'status' => StatusIzin::class,
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'siswa_id');
    }

    public function scopeStatus(Builder $query, StatusIzin|string|null $status): Builder
    {
        return $query->when($status, fn (Builder $q) => $q->where('status', $status));
    }

    /** Izin yang rentang tanggalnya beririsan dengan [$dari, $sampai]. */
    public function scopeBeririsan(Builder $query, string $dari, ?string $sampai = null): Builder
    {
        return $query->where('izin_dari', '<=', $sampai ?? $dari)->where('izin_sampai', '>=', $dari);
    }

    public function isPending(): bool
    {
        return $this->status === StatusIzin::Pending;
    }

    public function isOwnedBy(Siswa $siswa): bool
    {
        return (int) $this->siswa_id === (int) $siswa->siswa_id;
    }

    /** Lama izin dalam hari (inklusif). */
    protected function durasi(): Attribute
    {
        return Attribute::get(fn () => (int) $this->izin_dari->diffInDays($this->izin_sampai) + 1);
    }

    protected function buktiUrl(): Attribute
    {
        return Attribute::get(fn () => $this->bukti ? Storage::disk('public')->url($this->bukti) : null);
    }

    /** Rentang tanggal yang mudah dibaca, mis. "23 – 25 Agu 2025". */
    protected function periode(): Attribute
    {
        return Attribute::get(function () {
            $dari = $this->izin_dari;
            $sampai = $this->izin_sampai;

            if ($dari->isSameDay($sampai)) {
                return $dari->translatedFormat('j M Y');
            }

            $formatDari = match (true) {
                $dari->isSameMonth($sampai) => 'j',
                $dari->isSameYear($sampai) => 'j M',
                default => 'j M Y',
            };

            return $dari->translatedFormat($formatDari).' – '.$sampai->translatedFormat('j M Y');
        });
    }
}
