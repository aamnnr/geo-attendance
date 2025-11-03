<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Izin;
use Illuminate\Support\Facades\Auth;

class IzinApiController extends Controller
{
    // 🧑‍🎓 Siswa mengajukan izin
    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|string|max:100',
            'izin_dari' => 'required|date',
            'izin_sampai' => 'required|date|after_or_equal:izin_dari',
        ]);

        $siswa = Auth::user();

        $izin = Izin::create([
            'siswa_id' => $siswa->siswa_id,
            'keterangan' => $request->keterangan,
            'izin_dari' => $request->izin_dari,
            'izin_sampai' => $request->izin_sampai,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Izin berhasil diajukan.',
            'data' => $izin
        ]);
    }
}
