<?php

namespace Tests\Feature;

use App\Enums\Hari;
use App\Enums\StatusIzin;
use App\Enums\StatusPresensi;
use App\Models\Izin;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Rekap;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaPagesTest extends TestCase
{
    use RefreshDatabase;

    private Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $kelas = Kelas::factory()->create();
        $this->siswa = Siswa::factory()->create(['kelas_id' => $kelas->kelas_id]);

        Jadwal::factory()->create(['kelas_id' => $kelas->kelas_id, 'hari' => Hari::fromDate(now())]);
        Izin::factory()->create(['siswa_id' => $this->siswa->siswa_id, 'status' => StatusIzin::Pending]);
        Rekap::create([
            'siswa_id' => $this->siswa->siswa_id,
            'tanggal' => today()->subDay(),
            'jam_masuk' => '06:55:00',
            'jam_keluar' => '14:00:00',
            'status' => StatusPresensi::Hadir,
        ]);
    }

    public function test_landing_page_renders(): void
    {
        $this->get('/')->assertOk()->assertSee('Masuk sebagai Siswa');
    }

    public function test_every_siswa_page_renders(): void
    {
        $this->actingAs($this->siswa, 'siswa');

        foreach (['dashboard', 'presensi', 'rekap', 'rekap?bulan=2026-01', 'rekap?status=hadir', 'jadwal', 'izin', 'profil'] as $path) {
            $this->get("/siswa/{$path}")->assertOk();
        }
    }

    public function test_siswa_can_submit_izin(): void
    {
        $this->actingAs($this->siswa, 'siswa')
            ->post(route('siswa.izin.store'), [
                'jenis' => 'sakit',
                'keterangan' => 'Demam tinggi',
                'izin_dari' => today()->addDays(10)->toDateString(),
                'izin_sampai' => today()->addDays(11)->toDateString(),
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('izins', ['siswa_id' => $this->siswa->siswa_id, 'keterangan' => 'Demam tinggi', 'status' => 'pending']);
    }

    public function test_siswa_can_update_profile(): void
    {
        $this->actingAs($this->siswa, 'siswa')
            ->put(route('siswa.profile.update'), [
                'siswa_email' => 'baru@example.com',
                'siswa_hp' => '08123',
                'siswa_alamat' => 'Jl. Baru',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('baru@example.com', $this->siswa->fresh()->siswa_email);
    }
}
