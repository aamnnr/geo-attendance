<?php

namespace Tests\Unit;

use App\Enums\Hari;
use App\Support\Geo;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class GeoTest extends TestCase
{
    public function test_jarak_titik_yang_sama_adalah_nol(): void
    {
        $this->assertSame(0.0, Geo::distance(-7.7956, 110.3695, -7.7956, 110.3695));
    }

    public function test_jarak_satu_derajat_lintang_sekitar_111_km(): void
    {
        $this->assertEqualsWithDelta(111195, Geo::distance(0, 0, 1, 0), 50);
    }

    public function test_nama_hari_tidak_bergantung_locale(): void
    {
        $this->assertSame(Hari::Senin, Hari::fromDate(Carbon::parse('2026-10-26')));
        $this->assertSame(Hari::Minggu, Hari::fromDate(Carbon::parse('2026-11-01')));
    }
}
