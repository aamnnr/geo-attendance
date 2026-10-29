<?php

namespace App\Services;

use App\Enums\Hari;
use App\Enums\StatusPresensi;
use App\Exceptions\PresensiException;
use App\Models\Jadwal;
use App\Models\Libur;
use App\Models\Rekap;
use App\Models\Setting;
use App\Models\Siswa;
use App\Support\Geo;
use Carbon\CarbonInterface;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;

/**
 * Satu-satunya tempat aturan presensi berbasis lokasi. Dipakai oleh controller web & API.
 */
class PresensiService
{
    /**
     * Kondisi presensi siswa hari ini, untuk ditampilkan di UI.
     *
     * @return array{tanggal: CarbonInterface, rekap: ?Rekap, jadwal: Collection<int, Jadwal>, libur: ?Libur, izin: bool, aksi: string, pesan: string}
     */
    public function kondisiHariIni(Siswa $siswa, ?CarbonInterface $now = null): array
    {
        $now ??= now();
        $tanggal = $now->toDateString();

        $rekap = $this->rekapPada($siswa, $tanggal);
        $jadwal = Jadwal::untukHari($siswa->kelas_id, Hari::fromDate($now))->get();
        $libur = Libur::untukTanggal($tanggal);
        $izin = $siswa->sedangIzin($tanggal);

        [$aksi, $pesan] = match (true) {
            $libur !== null => ['libur', "Hari ini libur: {$libur->keterangan}."],
            $izin || in_array($rekap?->status, [StatusPresensi::Izin, StatusPresensi::Sakit], true) => ['izin', 'Anda tercatat izin hari ini.'],
            $jadwal->isEmpty() => ['tidak_ada_jadwal', 'Tidak ada jadwal pelajaran untuk kelas Anda hari ini.'],
            $rekap?->jam_masuk && $rekap?->jam_keluar => ['selesai', 'Presensi hari ini sudah lengkap. Sampai jumpa besok!'],
            $rekap?->jam_masuk !== null => ['pulang', 'Jangan lupa presensi pulang sebelum meninggalkan sekolah.'],
            $now->format('H:i') > $this->batasMasuk() => ['tutup', 'Waktu presensi masuk sudah ditutup (pukul '.$this->batasMasuk().').'],
            default => ['masuk', 'Silakan lakukan presensi masuk.'],
        };

        return ['tanggal' => $now] + compact('rekap', 'jadwal', 'libur', 'izin', 'aksi', 'pesan');
    }

    /**
     * Mencatat presensi masuk atau pulang secara otomatis sesuai kondisi hari ini.
     *
     * @return array{rekap: Rekap, tipe: 'masuk'|'pulang', pesan: string}
     *
     * @throws PresensiException
     */
    public function catat(Siswa $siswa, float $latitude, float $longitude, ?CarbonInterface $now = null): array
    {
        $now ??= now();
        $kondisi = $this->kondisiHariIni($siswa, $now);

        if (! in_array($kondisi['aksi'], ['masuk', 'pulang'], true)) {
            throw new PresensiException($kondisi['pesan']);
        }

        $jarak = $this->jarakDariSekolah($latitude, $longitude);
        $radius = (int) Setting::get('school_radius');

        if ($jarak > $radius) {
            throw new PresensiException("Anda berada {$jarak} m dari sekolah. Radius yang diizinkan {$radius} m.");
        }

        return $kondisi['aksi'] === 'masuk'
            ? $this->masuk($siswa, $kondisi['jadwal'], $latitude, $longitude, $jarak, $now)
            : $this->pulang($kondisi['rekap'], $kondisi['jadwal'], $latitude, $longitude, $now);
    }

    public function jarakDariSekolah(float $latitude, float $longitude): int
    {
        return (int) round(Geo::distance(
            (float) Setting::get('school_lat'),
            (float) Setting::get('school_lng'),
            $latitude,
            $longitude,
        ));
    }

    private function masuk(Siswa $siswa, Collection $jadwal, float $lat, float $lng, int $jarak, CarbonInterface $now): array
    {
        $batasTepatWaktu = $now->copy()
            ->setTimeFromTimeString($jadwal->first()->jam_mulai)
            ->addMinutes((int) Setting::get('toleransi_terlambat'));

        $status = $now->gt($batasTepatWaktu) ? StatusPresensi::Terlambat : StatusPresensi::Hadir;

        try {
            $rekap = Rekap::create([
                'siswa_id' => $siswa->siswa_id,
                'tanggal' => $now->toDateString(),
                'jam_masuk' => $now->format('H:i:s'),
                'status' => $status,
                'latitude' => $lat,
                'longitude' => $lng,
                'jarak' => $jarak,
            ]);
        } catch (UniqueConstraintViolationException) {
            // Dua request bersamaan (mis. tombol diklik dua kali).
            throw new PresensiException('Presensi masuk Anda sudah tercatat.');
        }

        return [
            'rekap' => $rekap,
            'tipe' => 'masuk',
            'pesan' => "Presensi masuk tercatat pukul {$now->format('H:i')} ({$status->label()}).",
        ];
    }

    private function pulang(Rekap $rekap, Collection $jadwal, float $lat, float $lng, CarbonInterface $now): array
    {
        $jamPulang = $now->copy()->setTimeFromTimeString($jadwal->max('jam_selesai'));

        $rekap->update([
            'jam_keluar' => $now->format('H:i:s'),
            'latitude_keluar' => $lat,
            'longitude_keluar' => $lng,
            'keterangan' => $now->lt($jamPulang) ? 'Pulang sebelum jam pelajaran berakhir' : $rekap->keterangan,
        ]);

        return [
            'rekap' => $rekap,
            'tipe' => 'pulang',
            'pesan' => "Presensi pulang tercatat pukul {$now->format('H:i')}. Hati-hati di jalan!",
        ];
    }

    private function rekapPada(Siswa $siswa, string $tanggal): ?Rekap
    {
        return $siswa->rekap()->where('tanggal', $tanggal)->first();
    }

    private function batasMasuk(): string
    {
        return (string) Setting::get('batas_presensi_masuk');
    }
}
