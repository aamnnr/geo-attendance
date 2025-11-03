<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasApiController extends Controller
{
    /**
     * 📘 Menampilkan daftar semua kelas
     */
    public function index()
    {
        $kelas = Kelas::select('id', 'nama_kelas', 'wali_kelas')->get();

        return response()->json([
            'status' => true,
            'data' => $kelas
        ], 200);
    }

    /**
     * 📘 Menampilkan detail kelas berdasarkan ID
     */
    public function show($id)
    {
        $kelas = Kelas::with('siswa:id,nama,nis,kelas_id')
                      ->find($id);

        if (!$kelas) {
            return response()->json([
                'status' => false,
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $kelas
        ], 200);
    }
}
