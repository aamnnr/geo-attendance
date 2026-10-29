<?php

namespace App\Enums;

enum StatusPresensi: string
{
    case Hadir = 'hadir';
    case Terlambat = 'terlambat';
    case Izin = 'izin';
    case Sakit = 'sakit';
    case Alpa = 'alpa';

    public function label(): string
    {
        return match ($this) {
            self::Hadir => 'Hadir',
            self::Terlambat => 'Terlambat',
            self::Izin => 'Izin',
            self::Sakit => 'Sakit',
            self::Alpa => 'Alpa',
        };
    }

    /** Warna badge (dipakai komponen <x-badge>). */
    public function color(): string
    {
        return match ($this) {
            self::Hadir => 'green',
            self::Terlambat => 'amber',
            self::Izin => 'blue',
            self::Sakit => 'violet',
            self::Alpa => 'red',
        };
    }

    /** Status yang dihitung sebagai "masuk sekolah". */
    public function isPresent(): bool
    {
        return in_array($this, [self::Hadir, self::Terlambat], true);
    }

    /** @return array<string, string> value => label */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $s) => [$s->value => $s->label()])->all();
    }
}
