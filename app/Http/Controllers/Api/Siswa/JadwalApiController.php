<?php
namespace App\Http\Controllers\Api\Siswa;  // 🔁 UBAH NAMESPACE

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class JadwalApiController extends Controller
{
    // 🧑‍🎓 Siswa melihat jadwal kelasnya
    public function jadwalSiswa(Request $request)
    {
        $siswa = $request->user();

        $jadwal = Jadwal::where('kelas_id', $siswa->kelas_id)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $jadwal
        ]);
    }
    
    // ❌ HAPUS method index(), store(), destroy() - itu untuk admin
}