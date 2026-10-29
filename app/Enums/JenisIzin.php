<?php

namespace App\Enums;

enum JenisIzin: string
{
    case Sakit = 'sakit';
    case Izin = 'izin';
    case Keluarga = 'keluarga';
    case Dispensasi = 'dispensasi';

    public function label(): string
    {
        return match ($this) {
            self::Sakit => 'Sakit',
            self::Izin => 'Izin Pribadi',
            self::Keluarga => 'Urusan Keluarga',
            self::Dispensasi => 'Dispensasi Sekolah',
        };
    }

    /** Status rekap yang dibuat saat izin jenis ini disetujui. */
    public function statusPresensi(): StatusPresensi
    {
        return $this === self::Sakit ? StatusPresensi::Sakit : StatusPresensi::Izin;
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $s) => [$s->value => $s->label()])->all();
    }
}
