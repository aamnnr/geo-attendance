<?php

namespace App\Enums;

use Carbon\CarbonInterface;

/**
 * Nama hari dalam Bahasa Indonesia, tidak bergantung pada locale Carbon.
 */
enum Hari: string
{
    case Senin = 'Senin';
    case Selasa = 'Selasa';
    case Rabu = 'Rabu';
    case Kamis = 'Kamis';
    case Jumat = 'Jumat';
    case Sabtu = 'Sabtu';
    case Minggu = 'Minggu';

    public static function fromDate(CarbonInterface $date): self
    {
        return self::cases()[$date->dayOfWeekIso - 1];
    }

    public function urutan(): int
    {
        return array_search($this, self::cases(), true) + 1;
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
