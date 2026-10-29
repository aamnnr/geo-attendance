<?php

namespace App\Services;

use App\Enums\Hari;
use App\Enums\StatusIzin;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\Izin;
use App\Models\Jadwal;
use App\Models\Rekap;
use App\Models\Siswa;
use Carbon\CarbonPeriod;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class IzinService
{
    /**
     * Siswa mengajukan izin baru.
     *
     * @param  array{jenis: string, keterangan: string, izin_dari: string, izin_sampai: string}  $data
     */
    public function ajukan(Siswa $siswa, array $data, ?UploadedFile $bukti = null): Izin
    {
        $this->pastikanTidakBentrok($siswa, $data['izin_dari'], $data['izin_sampai']);

        return $siswa->izin()->create([
            ...$data,
            'bukti' => $bukti?->store('bukti_izin', 'public'),
            'status' => StatusIzin::Pending,
        ]);
    }

    /**
     * Admin mencatat izin atas nama siswa (langsung disetujui).
     *
     * @param  array{siswa_id: int, jenis: string, keterangan: string, izin_dari: string, izin_sampai: string, catatan_admin?: ?string}  $data
     */
    public function catatOlehAdmin(Admin $admin, array $data, ?UploadedFile $bukti = null): Izin
    {
        $siswa = Siswa::findOrFail($data['siswa_id']);
        $izin = $this->ajukan($siswa, collect($data)->only(['jenis', 'keterangan', 'izin_dari', 'izin_sampai'])->all(), $bukti);

        return $this->setujui($izin, $admin, $data['catatan_admin'] ?? null);
    }

    public function setujui(Izin $izin, Admin $admin, ?string $catatan = null): Izin
    {
        $this->pastikanPending($izin);

        DB::transaction(function () use ($izin, $admin, $catatan) {
            $this->proses($izin, StatusIzin::Disetujui, $admin, $catatan);
            $this->buatRekapIzin($izin);
        });

        ActivityLog::record('izin_disetujui', "{$admin->admin_nama} menyetujui izin {$izin->siswa->siswa_nama} ({$izin->periode}).");

        return $izin;
    }

    public function tolak(Izin $izin, Admin $admin, ?string $catatan = null): Izin
    {
        $this->pastikanPending($izin);
        $this->proses($izin, StatusIzin::Ditolak, $admin, $catatan);

        ActivityLog::record('izin_ditolak', "{$admin->admin_nama} menolak izin {$izin->siswa->siswa_nama} ({$izin->periode}).");

        return $izin;
    }

    /** Siswa membatalkan izin yang belum diproses. */
    public function batalkan(Izin $izin): void
    {
        $this->pastikanPending($izin);

        if ($izin->bukti) {
            Storage::disk('public')->delete($izin->bukti);
        }

        $izin->delete();
    }

    private function proses(Izin $izin, StatusIzin $status, Admin $admin, ?string $catatan): void
    {
        $izin->update([
            'status' => $status,
            'admin_id' => $admin->admin_id,
            'catatan_admin' => $catatan,
            'diproses_pada' => now(),
        ]);
    }

    /**
     * Membuat rekap berstatus izin/sakit untuk setiap hari sekolah di rentang izin.
     * Hari tanpa jadwal dilewati, dan rekap yang sudah "hadir" tidak ditimpa.
     */
    private function buatRekapIzin(Izin $izin): void
    {
        $siswa = $izin->siswa;
        $hariSekolah = Jadwal::where('kelas_id', $siswa->kelas_id)->distinct()->pluck('hari')
            ->map(fn (Hari $hari) => $hari->value);

        $status = $izin->jenis->statusPresensi();

        foreach (CarbonPeriod::create($izin->izin_dari, $izin->izin_sampai) as $tanggal) {
            if (! $hariSekolah->contains(Hari::fromDate($tanggal)->value)) {
                continue;
            }

            $rekap = Rekap::where('siswa_id', $siswa->siswa_id)->where('tanggal', $tanggal->toDateString())->first();

            if ($rekap?->jam_masuk) {
                continue;
            }

            $rekap
                ? $rekap->update(['status' => $status, 'keterangan' => $izin->keterangan])
                : Rekap::create([
                    'siswa_id' => $siswa->siswa_id,
                    'tanggal' => $tanggal->toDateString(),
                    'status' => $status,
                    'keterangan' => $izin->keterangan,
                ]);
        }
    }

    private function pastikanPending(Izin $izin): void
    {
        if (! $izin->isPending()) {
            throw ValidationException::withMessages([
                'izin' => 'Izin ini sudah diproses sebelumnya.',
            ]);
        }
    }

    private function pastikanTidakBentrok(Siswa $siswa, string $dari, string $sampai): void
    {
        $bentrok = $siswa->izin()
            ->whereIn('status', [StatusIzin::Pending, StatusIzin::Disetujui])
            ->beririsan($dari, $sampai)
            ->exists();

        if ($bentrok) {
            throw ValidationException::withMessages([
                'izin_dari' => 'Sudah ada pengajuan izin pada rentang tanggal tersebut.',
            ]);
        }
    }
}
