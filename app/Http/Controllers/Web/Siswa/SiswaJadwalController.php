<?php

namespace App\Http\Controllers\Web\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jadwal; // <-- Import model Jadwal
use Carbon\Carbon;     // Import Carbon

class SiswaJadwalController extends Controller
{
    public function index()
    {
        // Ambil kelas_id siswa dari session
        $kelasId = session('siswa.kelas_id');

        // Cek jika session ada
        if (!$kelasId) {
            return redirect()->route('siswa.login')->with('error', 'Sesi Anda telah berakhir, silakan login kembali.');
        }

        // Ambil data jadwal berdasarkan kelas_id
        // PERBAIKAN: Gunakan nama variabel $jadwals (plural)
        $jadwals = Jadwal::where('kelas_id', $kelasId)
                        ->orderBy('jam_mulai', 'asc') 
                        ->get();
                        
        // Kirim data dengan nama 'jadwals' (plural)
        return view('siswa.jadwal', compact('jadwals'));
    }
}

