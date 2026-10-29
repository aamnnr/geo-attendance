<?php

namespace Tests\Feature;

use App\Enums\Hari;
use App\Enums\StatusPresensi;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\Izin;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Libur;
use App\Models\Rekap;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->admin = Admin::factory()->create();
        $kelas = Kelas::factory()->create(['kelas_nama' => 'X RPL 1']);
        $this->siswa = Siswa::factory()->create(['kelas_id' => $kelas->kelas_id]);
        Siswa::factory()->create(['kelas_id' => $kelas->kelas_id]);

        Jadwal::factory()->create(['kelas_id' => $kelas->kelas_id, 'hari' => Hari::Senin]);
        Jadwal::factory()->create(['kelas_id' => $kelas->kelas_id, 'hari' => Hari::Selasa, 'jam_mulai' => '09:00', 'jam_selesai' => '10:00']);
        Izin::factory()->create(['siswa_id' => $this->siswa->siswa_id]);
        Rekap::create([
            'siswa_id' => $this->siswa->siswa_id,
            'tanggal' => today()->toDateString(),
            'jam_masuk' => '06:55:00',
            'status' => StatusPresensi::Hadir,
            'jarak' => 20,
        ]);
        Libur::create(['keterangan' => 'Libur Semester', 'libur_dari' => today()->addDays(10), 'libur_sampai' => today()->addDays(12)]);
        ActivityLog::record('pengaturan', 'Admin memperbarui pengaturan.');
    }

    public static function halaman(): array
    {
        return [
            'dashboard' => ['admin.dashboard'],
            'siswa index' => ['admin.siswa.index'],
            'siswa create' => ['admin.siswa.create'],
            'kelas' => ['admin.kelas.index'],
            'jadwal' => ['admin.jadwal.index'],
            'rekap' => ['admin.rekap.index'],
            'izin' => ['admin.izin.index'],
            'libur' => ['admin.libur.index'],
            'log' => ['admin.log'],
            'pengaturan' => ['admin.settings.edit'],
        ];
    }

    #[DataProvider('halaman')]
    public function test_halaman_admin_dapat_dibuka(string $route): void
    {
        $this->actingAs($this->admin, 'admin')->get(route($route))->assertOk();
    }

    public function test_detail_dan_edit_siswa_dapat_dibuka(): void
    {
        $this->actingAs($this->admin, 'admin');

        $this->get(route('admin.siswa.show', $this->siswa))->assertOk()->assertSee($this->siswa->siswa_nama);
        $this->get(route('admin.siswa.edit', $this->siswa))->assertOk();
    }

    public function test_tamu_diarahkan_ke_login_admin(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_dapat_menambah_kelas(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.kelas.store'), ['kelas_nama' => 'XI TKJ 2', 'wali_kelas' => 'Bu Rina'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('kelas', ['kelas_nama' => 'XI TKJ 2']);
    }

    public function test_admin_dapat_menambah_siswa(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.siswa.store'), [
                'siswa_nama' => 'Rina Wulandari',
                'siswa_nis' => '99001',
                'kelas_id' => $this->siswa->kelas_id,
                'siswa_password' => 'rahasia123',
            ])
            ->assertRedirect(route('admin.siswa.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('siswas', ['siswa_nis' => '99001']);
    }
}
