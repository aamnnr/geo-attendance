<?php

namespace App\Models;

use App\Casts\DateOnly;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Libur extends Model
{
    use HasFactory;

    protected $primaryKey = 'libur_id';

    protected $fillable = ['keterangan', 'libur_dari', 'libur_sampai'];

    protected function casts(): array
    {
        return [
            'libur_dari' => DateOnly::class,
            'libur_sampai' => DateOnly::class,
        ];
    }

    public function scopeAktifPada(Builder $query, CarbonInterface|string $tanggal): Builder
    {
        $tanggal = $tanggal instanceof CarbonInterface ? $tanggal->toDateString() : $tanggal;

        return $query->where('libur_dari', '<=', $tanggal)->where('libur_sampai', '>=', $tanggal);
    }

    public static function untukTanggal(CarbonInterface|string $tanggal): ?self
    {
        return static::aktifPada($tanggal)->first();
    }
}
