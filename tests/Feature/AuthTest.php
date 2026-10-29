<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_halaman_terproteksi_mengarahkan_ke_login_yang_sesuai(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('admin.login'));
        $this->get('/siswa/dashboard')->assertRedirect(route('siswa.login'));
    }

    public function test_admin_login_dan_logout(): void
    {
        Admin::factory()->create(['admin_username' => 'admin', 'admin_password' => 'rahasia']);

        $this->post(route('admin.login.store'), ['username' => 'admin', 'password' => 'salah'])
            ->assertSessionHasErrors('username');
        $this->assertGuest('admin');

        $this->post(route('admin.login.store'), ['username' => 'admin', 'password' => 'rahasia'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated('admin');

        $this->post(route('admin.logout'))->assertRedirect(route('home'));
        $this->assertGuest('admin');
    }

    public function test_siswa_login_dengan_nis(): void
    {
        Siswa::factory()->create(['siswa_nis' => '22001', 'siswa_password' => '123456']);

        $this->post(route('siswa.login.store'), ['nis' => '22001', 'password' => '123456'])
            ->assertRedirect(route('siswa.dashboard'));
        $this->assertAuthenticated('siswa');
    }

    public function test_siswa_tidak_bisa_mengakses_panel_admin(): void
    {
        $siswa = Siswa::factory()->create();

        $this->actingAs($siswa, 'siswa')->get('/admin/dashboard')->assertRedirect(route('admin.login'));
    }

    public function test_login_dibatasi_setelah_lima_percobaan_gagal(): void
    {
        $this->freezeTime();
        Siswa::factory()->create(['siswa_nis' => '22001']);

        foreach (range(1, 5) as $_) {
            $this->post(route('siswa.login.store'), ['nis' => '22001', 'password' => 'salah']);
        }

        $this->post(route('siswa.login.store'), ['nis' => '22001', 'password' => 'salah'])
            ->assertSessionHasErrors(['nis' => 'Terlalu banyak percobaan login. Coba lagi dalam 60 detik.']);
    }
}
