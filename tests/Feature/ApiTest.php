<?php

namespace Tests\Feature;

use App\Enums\Hari;
use App\Models\Admin;
use App\Models\Jadwal;
use App\Models\Setting;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_login_presensi_dan_lihat_rekap_via_api(): void
    {
        $this->travelTo(Carbon::parse('2026-10-29 06:55:00'));
        Setting::put(['school_lat' => -7.7956, 'school_lng' => 110.3695, 'school_radius' => 100]);

        $siswa = Siswa::factory()->create(['siswa_nis' => '22001', 'siswa_password' => '123456']);
        Jadwal::factory()->create(['kelas_id' => $siswa->kelas_id, 'hari' => Hari::Kamis, 'jam_mulai' => '07:00']);

        $token = $this->postJson('/api/siswa/login', ['nis' => '22001', 'password' => '123456'])
            ->assertOk()
            ->json('token');

        $headers = ['Authorization' => "Bearer {$token}"];

        $this->getJson('/api/siswa/presensi', $headers)->assertOk()->assertJsonPath('data.aksi', 'masuk');

        $this->postJson('/api/siswa/presensi', ['latitude' => -7.79, 'longitude' => 110.3695], $headers)
            ->assertUnprocessable()
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'Radius'));

        $this->postJson('/api/siswa/presensi', ['latitude' => -7.7956, 'longitude' => 110.3695], $headers)
            ->assertCreated()
            ->assertJsonPath('tipe', 'masuk')
            ->assertJsonPath('data.status', 'hadir');

        $this->getJson('/api/siswa/rekap', $headers)->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/siswa/jadwal', $headers)->assertOk()->assertJsonPath('data.0.hari', 'Kamis');
    }

    public function test_token_siswa_tidak_bisa_mengakses_endpoint_admin(): void
    {
        $siswa = Siswa::factory()->create();
        $token = $siswa->createToken('test', ['siswa'])->plainTextToken;

        $this->getJson('/api/admin/siswa', ['Authorization' => "Bearer {$token}"])->assertForbidden();
    }

    public function test_admin_crud_siswa_via_api(): void
    {
        $admin = Admin::factory()->create(['admin_username' => 'admin', 'admin_password' => 'password']);
        $token = $this->postJson('/api/admin/login', ['username' => 'admin', 'password' => 'password'])->json('token');
        $headers = ['Authorization' => "Bearer {$token}"];

        $kelasId = $this->postJson('/api/admin/kelas', ['kelas_nama' => 'X RPL 2'], $headers)->assertCreated()->json('data.id');

        $siswaId = $this->postJson('/api/admin/siswa', [
            'siswa_nama' => 'Rina',
            'siswa_nis' => '99001',
            'kelas_id' => $kelasId,
            'siswa_password' => 'rahasia1',
        ], $headers)->assertCreated()->assertJsonPath('data.kelas.nama', 'X RPL 2')->json('data.id');

        $this->assertArrayNotHasKey('siswa_password', Siswa::find($siswaId)->toArray());

        $this->postJson('/api/admin/siswa', ['siswa_nama' => 'Duplikat', 'siswa_nis' => '99001', 'kelas_id' => $kelasId, 'siswa_password' => 'rahasia1'], $headers)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('siswa_nis');

        $this->deleteJson("/api/admin/kelas/{$kelasId}", [], $headers)->assertConflict();
        $this->deleteJson("/api/admin/siswa/{$siswaId}", [], $headers)->assertOk();
        $this->getJson('/api/admin/dashboard', $headers)->assertOk()->assertJsonStructure(['data' => ['hari_ini', 'tren_7_hari']]);
    }
}
