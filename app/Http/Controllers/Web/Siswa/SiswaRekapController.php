<?php

namespace App\Http\Controllers\Web\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rekap; // <-- 1. Import model Rekap
use Carbon\Carbon;     // <-- 2. Import Carbon untuk penanggalan

class SiswaRekapController extends Controller
{
    /**
     * Menampilkan halaman rekap presensi milik siswa.
     */
    public function index(Request $request)
    {
        // 3. Ambil ID siswa dari session yang Anda buat saat login
        $siswaId = session('siswa.siswa_id');

        if (!$siswaId) {
            // Jika session tidak ditemukan, kembali ke login
            return redirect()->route('siswa.login')->with('error', 'Sesi Anda telah berakhir, silakan login kembali.');
        }

        // 4. Ambil data rekap HANYA milik siswa ini
        // Kita urutkan berdasarkan tanggal terbaru dan gunakan pagination
        $rekaps = Rekap::where('siswa_id', $siswaId)
                        ->orderBy('tanggal', 'desc') // Urutkan dari terbaru
                        ->paginate(15); // Tampilkan 15 data per halaman

        // 5. Kirim data $rekaps ke view
        return view('siswa.rekap', [
            'rekaps' => $rekaps
        ]);
    }
}
