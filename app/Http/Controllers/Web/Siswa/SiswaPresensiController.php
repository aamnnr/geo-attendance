<?php

namespace App\Http\Controllers\Web\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting; // <-- 1. Ambil setting
use App\Models\Jadwal;  // <-- 2. Cek jadwal
use App\Models\Rekap;   // <-- 3. Simpan rekap
use Carbon\Carbon;

class SiswaPresensiController extends Controller
{
    /**
     * Menampilkan halaman presensi.
     */
    public function index()
    {
        return view('siswa.presensi');
    }

    /**
     * Menyimpan data presensi (masuk atau pulang).
     */
    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // --- 1. AMBIL DATA PENGATURAN & SISWA ---
        $siswaId = session('siswa.siswa_id');
        $kelasId = session('siswa.kelas_id');
        if (!$siswaId || !$kelasId) {
            return back()->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        // Ambil pengaturan sekolah (koordinat & radius) dari database
        $settings = Setting::pluck('value', 'key');
        $schoolLat = (float) $settings->get('school_lat', 0);
        $schoolLng = (float) $settings->get('school_lng', 0);
        $schoolRadius = (float) $settings->get('school_radius', 100); // Default 100m

        // Koordinat siswa dari form
        $siswaLat = (float) $request->latitude;
        $siswaLng = (float) $request->longitude;

        // --- 2. HITUNG JARAK ---
        $jarak = $this->hitungJarak($schoolLat, $schoolLng, $siswaLat, $siswaLng);

        // --- 3. VALIDASI JARAK ---
        if ($jarak > $schoolRadius) {
            return back()->with('error', "Presensi Gagal. Anda berada " . (int)$jarak . " meter dari lokasi. (Radius diizinkan: {$schoolRadius}m)");
        }

        // --- 4. CEK HARI & JAM (LOGIKA MASUK/PULANG) ---
        $todayName = Carbon::now()->isoFormat('dddd'); // 'Senin', 'Selasa', dst.
        $currentTime = Carbon::now();
        $todayDate = $currentTime->toDateString(); // 'Y-m-d'

        // Cari jadwal hari ini untuk kelas siswa
        $jadwalMasuk = Jadwal::where('kelas_id', $kelasId)
                            ->where('hari', $todayName)
                            ->orderBy('jam_mulai', 'asc')
                            ->first();

        $jadwalPulang = Jadwal::where('kelas_id', $kelasId)
                            ->where('hari', $todayName)
                            ->orderBy('jam_selesai', 'desc')
                            ->first();

        if (!$jadwalMasuk) {
            return back()->with('error', 'Presensi Gagal. Tidak ada jadwal pelajaran untuk kelas Anda hari ini.');
        }

        // --- 5. CEK REKAP HARI INI (Sudah presensi atau belum?) ---
        $rekapHariIni = Rekap::where('siswa_id', $siswaId)
                            ->where('tanggal', $todayDate)
                            ->first();
                            
        // Tentukan jam batas (misal: jam 12 siang)
        $batasMasukPulang = Carbon::parse($todayDate . ' 12:00:00');

        if ($rekapHariIni) {
            // --- LOGIKA PRESENSI PULANG ---
            if ($rekapHariIni->jam_masuk && !$rekapHariIni->jam_keluar) {
                // Siswa sudah presensi masuk, sekarang presensi pulang
                
                // Cek apakah sudah jam pulang
                $jamPulang = Carbon::parse($todayDate . ' ' . $jadwalPulang->jam_selesai);
                if ($currentTime->lt($jamPulang)) {
                    // return back()->with('error', 'Belum waktunya pulang. Jam pulang Anda adalah ' . $jadwalPulang->jam_selesai);
                    // (Kita izinkan pulang cepat, tapi catat)
                }

                $rekapHariIni->update([
                    'jam_keluar' => $currentTime->toTimeString(),
                    'status' => $rekapHariIni->status // Status tidak berubah (tetap Hadir/Terlambat)
                ]);

                return back()->with('success', 'Presensi PULANG berhasil dicatat pukul ' . $currentTime->toTimeString());

            } else if ($rekapHariIni->jam_masuk && $rekapHariIni->jam_keluar) {
                // Siswa sudah presensi masuk DAN pulang.
                return back()->with('error', 'Anda sudah melakukan presensi masuk dan pulang hari ini.');
            }

        } else {
            // --- LOGIKA PRESENSI MASUK ---
            
            // Cek apakah siswa mencoba presensi 'masuk' terlalu siang
            if ($currentTime->gt($batasMasukPulang)) {
                return back()->with('error', 'Waktu presensi MASUK sudah berakhir (pukul 12:00).');
            }

            // Tentukan status (Hadir atau Terlambat)
            $jamMasuk = Carbon::parse($todayDate . ' ' . $jadwalMasuk->jam_mulai);
            $status = 'Hadir';
            if ($currentTime->gt($jamMasuk->addMinutes(15))) { // Toleransi 15 menit
                $status = 'Terlambat';
            }

            // Buat rekap baru
            Rekap::create([
                'siswa_id' => $siswaId,
                'tanggal' => $todayDate,
                'jam_masuk' => $currentTime->toTimeString(),
                'jam_keluar' => null,
                'status' => $status,
                'latitude' => $siswaLat,
                'longitude' => $siswaLng,
            ]);

            return back()->with('success', "Presensi MASUK (Status: {$status}) berhasil dicatat pukul " . $currentTime->toTimeString());
        }

        return back()->with('error', 'Terjadi kesalahan yang tidak diketahui. Silakan coba lagi.');
    }


    /**
     * Fungsi Haversine untuk menghitung jarak antara dua titik koordinat.
     * Mengembalikan jarak dalam METER.
     */
    private function hitungJarak($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        return $angle * $earthRadius;
    }
}

