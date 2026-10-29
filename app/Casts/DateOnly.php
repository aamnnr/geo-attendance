<?php

namespace App\Casts;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Kolom DATE yang selalu disimpan sebagai "Y-m-d".
 *
 * Cast bawaan 'date' menyimpan "Y-m-d 00:00:00", yang di SQLite membuat
 * perbandingan `where('tanggal', '2025-01-01')` dan unique index tidak konsisten.
 */
class DateOnly implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?CarbonImmutable
    {
        return $value === null ? null : CarbonImmutable::parse($value)->startOfDay();
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return $value === null ? null : CarbonImmutable::parse($value)->toDateString();
    }

    public function serialize(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return $value?->toDateString();
    }
}
