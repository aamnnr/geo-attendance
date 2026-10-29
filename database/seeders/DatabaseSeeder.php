<?php

namespace Database\Seeders;

use App\Enums\Hari;
use App\Enums\StatusIzin;
use App\Enums\StatusPresensi;
use App\Models\Admin;
use App\Models\Izin;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Libur;
use App\Models\Rekap;
use App\Models\Siswa;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

/**
 * Data demo. Jalankan dengan: php artisan migrate:fresh --seed
 *
 * Akun default:
 *  - Admin : admin / password
 *  - Siswa : 22001 / 123456 (semua siswa memakai password 123456)
 */
class DatabaseSeeder extends Seeder
{
    private const MAPEL = [
        ['Matematika', 'Dra. Sri Wahyuni'],
        ['Bahasa Indonesia', 'Budi Hartono, S.Pd.'],
        ['Bahasa Inggris', 'Rina Marlina, S.Pd.'],
        ['Pemrograman Web', 'Andi Pratama, S.Kom.'],
        ['Basis Data', 'Dewi Kusuma, S.Kom.'],
        ['Jaringan Dasar', 'Hendra Saputra, S.T.'],
        ['Pendidikan Agama', 'H. Ahmad Fauzi, S.Ag.'],
        ['PJOK', 'Rudi Hermawan, S.Pd.'],
    ];

    public function run(): void
    {
        Admin::create([
            'admin_username' => 'admin',
            'admin_password' => 'password',
            'admin_nama' => 'Administrator',
        ]);

        $kelasList = collect(['X RPL 1', 'X TKJ 1', 'XI RPL 1', 'XI TKJ 1', 'XII RPL 1', 'XII TKJ 1'])
            ->map(fn ($nama) => Kelas::factory()->create(['kelas_nama' => $nama]));

        $this->seedJadwal($kelasList);
        $siswas = $this->seedSiswa($kelasList);
        $this->seedRekap($siswas);
        $this->seedIzin($siswas);

        Libur::create([
            'keterangan' => 'Libur Semester Ganjil',
            'libur_dari' => today()->addMonths(2)->startOfMonth(),
            'libur_sampai' => today()->addMonths(2)->startOfMonth()->addDays(13),
        ]);
    }

    private function seedJadwal($kelasList): void
    {
        $slots = [['07:00', '08:30'], ['08:30', '10:00'], ['10:15', '11:45'], ['12:30', '14:00']];
        $hariSekolah = [Hari::Senin, Hari::Selasa, Hari::Rabu, Hari::Kamis, Hari::Jumat];

        foreach ($kelasList as $kelas) {
            foreach ($hariSekolah as $hari) {
                $mapel = collect(self::MAPEL)->shuffle();
                $jumlahSlot = $hari === Hari::Jumat ? 2 : 4;

                foreach (array_slice($slots, 0, $jumlahSlot) as $i => [$mulai, $selesai]) {
                    Jadwal::create([
                        'kelas_id' => $kelas->kelas_id,
                        'hari' => $hari,
                        'mapel' => $mapel[$i][0],
                        'guru' => $mapel[$i][1],
                        'jam_mulai' => $mulai,
                        'jam_selesai' => $selesai,
                    ]);
                }
            }
        }
    }

    private function seedSiswa($kelasList)
    {
        // Siswa contoh dengan NIS tetap agar mudah dipakai login.
        $siswas = collect([
            ['Budi Santoso', '22001'],
            ['Citra Lestari', '22002'],
            ['Agus Setiawan', '22003'],
        ])->map(fn ($s, $i) => Siswa::factory()->create([
            'siswa_nama' => $s[0],
            'siswa_nis' => $s[1],
            'kelas_id' => $kelasList[$i % 2]->kelas_id,
            'siswa_email' => strtolower(explode(' ', $s[0])[0]).'@example.com',
        ]));

        foreach ($kelasList as $kelas) {
            $siswas = $siswas->merge(Siswa::factory()->count(6)->create(['kelas_id' => $kelas->kelas_id]));
        }

        return $siswas;
    }

    /** Rekap 14 hari sekolah terakhir dengan distribusi status yang realistis. */
    private function seedRekap($siswas): void
    {
        $rows = [];
        $now = now();

        foreach (CarbonPeriod::create(today()->subDays(20), today()->subDay()) as $tanggal) {
            if ($tanggal->isWeekend()) {
                continue;
            }

            foreach ($siswas as $siswa) {
                $acak = fake()->numberBetween(1, 100);
                $status = match (true) {
                    $acak <= 78 => StatusPresensi::Hadir,
                    $acak <= 88 => StatusPresensi::Terlambat,
                    $acak <= 93 => StatusPresensi::Izin,
                    $acak <= 96 => StatusPresensi::Sakit,
                    default => StatusPresensi::Alpa,
                };

                $hadir = $status->isPresent();
                $rows[] = [
                    'siswa_id' => $siswa->siswa_id,
                    'tanggal' => $tanggal->toDateString(),
                    'status' => $status->value,
                    'jam_masuk' => $hadir ? ($status === StatusPresensi::Hadir ? '06:'.fake()->numberBetween(30, 59) : '07:'.fake()->numberBetween(16, 45)).':00' : null,
                    'jam_keluar' => $hadir ? '14:'.str_pad(fake()->numberBetween(0, 30), 2, '0', STR_PAD_LEFT).':00' : null,
                    'jarak' => $hadir ? fake()->numberBetween(5, 90) : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            Rekap::insert($chunk);
        }
    }

    private function seedIzin($siswas): void
    {
        $admin = Admin::first();

        Izin::factory()->count(6)->sequence(fn ($seq) => ['siswa_id' => $siswas[$seq->index]->siswa_id])->create([
            'izin_dari' => today()->addDay(),
            'izin_sampai' => today()->addDays(2),
        ]);

        Izin::factory()->count(5)->sequence(fn ($seq) => ['siswa_id' => $siswas[$seq->index + 6]->siswa_id])->create([
            'status' => StatusIzin::Disetujui,
            'admin_id' => $admin->admin_id,
            'izin_dari' => today()->subDays(5),
            'izin_sampai' => today()->subDays(4),
            'diproses_pada' => now()->subDays(6),
        ]);

        Izin::factory()->count(3)->sequence(fn ($seq) => ['siswa_id' => $siswas[$seq->index + 11]->siswa_id])->create([
            'status' => StatusIzin::Ditolak,
            'admin_id' => $admin->admin_id,
            'izin_dari' => today()->subDays(3),
            'izin_sampai' => today()->subDays(3),
            'catatan_admin' => 'Keterangan kurang jelas, mohon lampirkan bukti.',
            'diproses_pada' => now()->subDays(4),
        ]);
    }
}
