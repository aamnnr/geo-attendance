<?php

namespace App\Models;

use App\Enums\StatusIzin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Siswa extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $primaryKey = 'siswa_id';

    protected $authPasswordName = 'siswa_password';

    protected $fillable = [
        'siswa_nama',
        'siswa_nis',
        'kelas_id',
        'siswa_password',
        'siswa_email',
        'siswa_hp',
        'siswa_alamat',
    ];

    protected $hidden = [
        'siswa_password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'siswa_password' => 'hashed',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }

    public function rekap(): HasMany
    {
        return $this->hasMany(Rekap::class, 'siswa_id', 'siswa_id');
    }

    public function izin(): HasMany
    {
        return $this->hasMany(Izin::class, 'siswa_id', 'siswa_id');
    }

    /** Apakah siswa punya izin disetujui yang mencakup tanggal tertentu. */
    public function sedangIzin(string $tanggal): bool
    {
        return $this->izin()
            ->where('status', StatusIzin::Disetujui)
            ->beririsan($tanggal)
            ->exists();
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $q) => $q->where(function (Builder $q) use ($term) {
            $q->where('siswa_nama', 'like', "%{$term}%")
                ->orWhere('siswa_nis', 'like', "%{$term}%");
        }));
    }

    /** Inisial nama untuk avatar, mis. "Budi Santoso" -> "BS". */
    protected function inisial(): Attribute
    {
        return Attribute::get(fn () => Str::of($this->siswa_nama)
            ->explode(' ')
            ->take(2)
            ->map(fn ($kata) => Str::upper(Str::substr($kata, 0, 1)))
            ->implode(''));
    }
}
