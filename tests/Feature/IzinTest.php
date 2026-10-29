<?php

namespace Tests\Feature;

use App\Enums\Hari;
use App\Enums\StatusIzin;
use App\Enums\StatusPresensi;
use App\Models\Admin;
use App\Models\Izin;
use App\Models\Jadwal;
use App\Models\Rekap;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IzinTest extends TestCase
{
    use RefreshDatabase;

    private Siswa $siswa;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->travelTo(Carbon::parse('2026-10-26 08:00:00')); // Senin

        $this->admin = Admin::factory()->create();
        $this->siswa = Siswa::factory()->create();

        foreach ([Hari::Senin, Hari::Selasa, Hari::Rabu] as $hari) {
            Jadwal::factory()->create(['kelas_id' => $this->siswa->kelas_id, 'hari' => $hari]);
        }
    }

    public function test_siswa_mengajukan_izin_dengan_bukti(): void
    {
        Storage::fake('public');

        $this->actingAs($this->siswa, 'siswa')
            ->post(route('siswa.izin.store'), [
                'jenis' => 'sakit',
                'keterangan' => 'Demam',
                'izin_dari' => '2026-10-27',
                'izin_sampai' => '2026-10-28',
                'bukti' => UploadedFile::fake()->image('surat.jpg'),
            ])
            ->assertSessionHas('success');

        $izin = Izin::sole();
        $this->assertSame(StatusIzin::Pending, $izin->status);
        $this->assertSame(2, $izin->durasi);
        Storage::disk('public')->assertExists($izin->bukti);
    }

    public function test_pengajuan_yang_bentrok_ditolak(): void
    {
        Izin::factory()->create(['siswa_id' => $this->siswa->siswa_id, 'izin_dari' => '2026-10-27', 'izin_sampai' => '2026-10-29']);

        $this->actingAs($this->siswa, 'siswa')
            ->post(route('siswa.izin.store'), ['jenis' => 'izin', 'keterangan' => 'x', 'izin_dari' => '2026-10-28', 'izin_sampai' => '2026-10-28'])
            ->assertSessionHasErrors('izin_dari');
    }

    public function test_admin_menyetujui_izin_membuat_rekap_hanya_di_hari_sekolah(): void
    {
        // Selasa s/d Kamis; Kamis tidak ada jadwal.
        $izin = Izin::factory()->create([
            'siswa_id' => $this->siswa->siswa_id,
            'jenis' => 'sakit',
            'izin_dari' => '2026-10-27',
            'izin_sampai' => '2026-10-29',
        ]);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.izin.approve', $izin))
            ->assertSessionHas('success');

        $izin->refresh();
        $this->assertSame(StatusIzin::Disetujui, $izin->status);
        $this->assertSame($this->admin->admin_id, $izin->admin_id);
        $this->assertNotNull($izin->diproses_pada);

        $this->assertSame(['2026-10-27', '2026-10-28'], Rekap::orderBy('tanggal')->pluck('tanggal')->map->toDateString()->all());
        $this->assertTrue(Rekap::all()->every(fn ($r) => $r->status === StatusPresensi::Sakit));
        $this->assertDatabaseHas('activity_logs', ['tipe' => 'izin_disetujui']);
    }

    public function test_izin_yang_sudah_diproses_tidak_bisa_diproses_ulang(): void
    {
        $izin = Izin::factory()->create(['siswa_id' => $this->siswa->siswa_id, 'status' => StatusIzin::Ditolak]);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.izin.approve', $izin))
            ->assertSessionHasErrors('izin');

        $this->assertSame(StatusIzin::Ditolak, $izin->fresh()->status);
    }

    public function test_admin_menolak_dengan_catatan(): void
    {
        $izin = Izin::factory()->create(['siswa_id' => $this->siswa->siswa_id]);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.izin.reject', $izin), ['catatan_admin' => 'Lampirkan surat dokter'])
            ->assertSessionHas('success');

        $this->assertSame('Lampirkan surat dokter', $izin->fresh()->catatan_admin);
        $this->assertDatabaseCount('rekaps', 0);
    }

    public function test_siswa_hanya_bisa_membatalkan_izin_miliknya_yang_pending(): void
    {
        $milikOrangLain = Izin::factory()->create();
        $milikSendiri = Izin::factory()->create(['siswa_id' => $this->siswa->siswa_id]);

        $this->actingAs($this->siswa, 'siswa')->delete(route('siswa.izin.destroy', $milikOrangLain))->assertForbidden();
        $this->actingAs($this->siswa, 'siswa')->delete(route('siswa.izin.destroy', $milikSendiri))->assertSessionHas('success');

        $this->assertModelMissing($milikSendiri);
        $this->assertModelExists($milikOrangLain);
    }

    public function test_admin_mencatat_izin_langsung_disetujui(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.izin.store'), [
                'siswa_id' => $this->siswa->siswa_id,
                'jenis' => 'dispensasi',
                'keterangan' => 'Lomba LKS',
                'izin_dari' => '2026-10-26',
                'izin_sampai' => '2026-10-26',
            ])
            ->assertSessionHas('success');

        $this->assertSame(StatusIzin::Disetujui, Izin::sole()->status);
        $this->assertSame(StatusPresensi::Izin, Rekap::sole()->status);
    }
}
