<?php
namespace App\Http\Controllers\Api\Siswa;  // 🔁 UBAH NAMESPACE

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rekap;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RekapApiController extends Controller
{
    // 🧑‍🎓 Siswa melihat riwayat presensinya sendiri
    public function rekapSiswa(Request $request)
    {
        $siswa = Auth::user();
        
        $rekap = Rekap::where('siswa_id', $siswa->siswa_id)
                      ->orderBy('tanggal', 'desc')
                      ->get();

        return response()->json([
            'status' => 'success',
            'data' => $rekap
        ]);
    }
    
    // ❌ HAPUS method store() - sudah ada di PresensiController
    // ❌ HAPUS method index() - itu untuk admin
    // ❌ HAPUS method distance() - duplikat
}