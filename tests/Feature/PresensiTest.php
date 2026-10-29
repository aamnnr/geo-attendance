<?php

namespace Tests\Feature;

use App\Enums\Hari;
use App\Enums\StatusIzin;
use App\Enums\StatusPresensi;
use App\Models\Izin;
use App\Models\Jadwal;
use App\Models\Libur;
use App\Models\Rekap;
use App\Models\Setting;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PresensiTest extends TestCase
{
    use RefreshDatabase;

    private const SEKOLAH = ['lat' => -7.7956, 'lng' => 110.3695];

    private Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        // Kamis, 29 Okt 2026 pukul 06:50 (sebelum jam pertama).
        $this->travelTo(Carbon::parse('2026-10-29 06:50:00'));

        Setting::put(['school_lat' => self::SEKOLAH['lat'], 'school_lng' => self::SEKOLAH['lng'], 'school_radius' => 100]);

        $this->siswa = Siswa::factory()->create();
        Jadwal::factory()->create([
            'kelas_id' => $this->siswa->kelas_id,
            'hari' => Hari::Kamis,
            'jam_mulai' => '07:00',
            'jam_selesai' => '13:00',
        ]);
    }

    private function presensi(float $lat = self::SEKOLAH['lat'], float $lng = self::SEKOLAH['lng'])
    {
        return $this->actingAs($this->siswa, 'siswa')
            ->from(route('siswa.presensi'))
            ->post(route('siswa.presensi.store'), ['latitude' => $lat, 'longitude' => $lng]);
    }

    public function test_presensi_masuk_tepat_waktu_tercatat_hadir(): void
    {
        $this->presensi()->assertRedirect(route('siswa.dashboard'))->assertSessionHas('success');

        $rekap = Rekap::sole();
        $this->assertSame(StatusPresensi::Hadir, $rekap->status);
        $this->assertSame('06:50:00', $rekap->jam_masuk);
        $this->assertSame('2026-10-29', $rekap->getRawOriginal('tanggal'));
    }

    public function test_presensi_setelah_toleransi_tercatat_terlambat(): void
    {
        $this->travelTo(Carbon::parse('2026-10-29 07:20:00'));

        $this->presensi()->assertSessionHas('success');

        $this->assertSame(StatusPresensi::Terlambat, Rekap::sole()->status);
    }

    public function test_presensi_di_luar_radius_ditolak(): void
    {
        // ~1,1 km ke utara
        $this->presensi(self::SEKOLAH['lat'] + 0.01)
            ->assertRedirect(route('siswa.presensi'))
            ->assertSessionHas('error', fn ($pesan) => str_contains($pesan, 'Radius yang diizinkan 100 m'));

        $this->assertDatabaseCount('rekaps', 0);
    }

    public function test_presensi_kedua_mencatat_pulang_lalu_ketiga_ditolak(): void
    {
        $this->presensi();

        $this->travelTo(Carbon::parse('2026-10-29 13:05:00'));
        $this->presensi()->assertSessionHas('success');
        $this->assertSame('13:05:00', Rekap::sole()->jam_keluar);
        $this->assertNull(Rekap::sole()->keterangan);

        $this->presensi()->assertSessionHas('error');
    }

    public function test_pulang_lebih_awal_diberi_keterangan(): void
    {
        $this->presensi();
        $this->travelTo(Carbon::parse('2026-10-29 10:00:00'));
        $this->presensi();

        $this->assertNotNull(Rekap::sole()->keterangan);
    }

    public function test_presensi_masuk_ditutup_setelah_batas(): void
    {
        $this->travelTo(Carbon::parse('2026-10-29 12:30:00'));

        $this->presensi()->assertSessionHas('error', fn ($pesan) => str_contains($pesan, 'ditutup'));
        $this->assertDatabaseCount('rekaps', 0);
    }

    public function test_presensi_ditolak_saat_hari_libur(): void
    {
        Libur::create(['keterangan' => 'Libur Nasional', 'libur_dari' => '2026-10-29', 'libur_sampai' => '2026-10-29']);

        $this->presensi()->assertSessionHas('error', fn ($pesan) => str_contains($pesan, 'Libur Nasional'));
    }

    public function test_presensi_ditolak_tanpa_jadwal(): void
    {
        $this->travelTo(Carbon::parse('2026-10-31 07:00:00')); // Sabtu

        $this->presensi()->assertSessionHas('error', fn ($pesan) => str_contains($pesan, 'Tidak ada jadwal'));
    }

    public function test_presensi_ditolak_saat_sedang_izin(): void
    {
        Izin::factory()->create([
            'siswa_id' => $this->siswa->siswa_id,
            'status' => StatusIzin::Disetujui,
            'izin_dari' => '2026-10-28',
            'izin_sampai' => '2026-10-30',
        ]);

        $this->presensi()->assertSessionHas('error', fn ($pesan) => str_contains($pesan, 'izin'));
    }

    public function test_koordinat_wajib_diisi(): void
    {
        $this->actingAs($this->siswa, 'siswa')
            ->post(route('siswa.presensi.store'))
            ->assertSessionHasErrors(['latitude', 'longitude']);
    }

    public function test_command_alpa_hanya_menandai_siswa_yang_tidak_hadir_dan_tidak_izin(): void
    {
        $hadir = $this->siswa;
        $this->presensi();

        $izin = Siswa::factory()->create(['kelas_id' => $hadir->kelas_id]);
        Izin::factory()->create(['siswa_id' => $izin->siswa_id, 'status' => StatusIzin::Disetujui, 'izin_dari' => '2026-10-29', 'izin_sampai' => '2026-10-29']);

        $bolos = Siswa::factory()->create(['kelas_id' => $hadir->kelas_id]);
        $kelasLain = Siswa::factory()->create(); // kelas tanpa jadwal hari Kamis

        $this->artisan('rekap:alpa')->assertSuccessful();

        $this->assertSame(StatusPresensi::Alpa, Rekap::where('siswa_id', $bolos->siswa_id)->sole()->status);
        $this->assertDatabaseMissing('rekaps', ['siswa_id' => $izin->siswa_id]);
        $this->assertDatabaseMissing('rekaps', ['siswa_id' => $kelasLain->siswa_id]);
        $this->assertSame(StatusPresensi::Hadir, Rekap::where('siswa_id', $hadir->siswa_id)->sole()->status);
    }
}
