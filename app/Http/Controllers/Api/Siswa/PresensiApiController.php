<?php
namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rekap;
use Carbon\Carbon;

class PresensiApiController extends Controller
{
    // 🔹 Fungsi hitung jarak antar dua titik (Haversine Formula)
    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    // 🟢 PRESENSI MASUK BERDASARKAN LOKASI
    public function masuk(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $siswa = Auth::user();

        // Koordinat sekolah dari .env
        $schoolLat = env('SCHOOL_LAT');
        $schoolLng = env('SCHOOL_LNG');
        $radius = env('SCHOOL_RADIUS', 100); // meter

        // Hitung jarak dari sekolah
        $distance = $this->distance($schoolLat, $schoolLng, $request->latitude, $request->longitude);

        // Cek apakah dalam radius sekolah
        if ($distance > $radius) {
            return response()->json([
                'status' => 'error',
                'message' => 'Presensi gagal: Anda berada di luar area sekolah (' . round($distance) . ' m).'
            ], 403);
        }

        $today = Carbon::today()->toDateString();
        
        // Cek apakah sudah presensi masuk hari ini
        $existing = Rekap::where('siswa_id', $siswa->siswa_id)
                         ->where('tanggal', $today)
                         ->first();

        if ($existing && $existing->jam_masuk) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah melakukan presensi masuk hari ini.'
            ], 409);
        }

        // Simpan presensi masuk
        $rekap = Rekap::updateOrCreate(
            ['siswa_id' => $siswa->siswa_id, 'tanggal' => $today],
            [
                'jam_masuk' => Carbon::now()->format('H:i:s'),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'status' => 'Hadir'
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Presensi masuk berhasil!',
            'data' => $rekap
        ]);
    }

    // 🔴 PRESENSI PULANG BERDASARKAN LOKASI
    public function pulang(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $siswa = Auth::user();
        $today = Carbon::today()->toDateString();

        // Cek data presensi masuk
        $rekap = Rekap::where('siswa_id', $siswa->siswa_id)
                      ->where('tanggal', $today)
                      ->first();

        if (!$rekap) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda belum melakukan presensi masuk.'
            ], 404);
        }

        if ($rekap->jam_keluar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah melakukan presensi pulang.'
            ], 409);
        }

        // Update presensi pulang
        $rekap->update([
            'jam_keluar' => Carbon::now()->format('H:i:s'),
            'latitude_pulang' => $request->latitude,
            'longitude_pulang' => $request->longitude,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Presensi pulang berhasil!',
            'data' => $rekap
        ]);
    }
}