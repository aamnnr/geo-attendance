<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    /**
     * Menangani permintaan login siswa untuk API.
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        // CATATAN: Sesuaikan 'siswa_nis' jika Anda menggunakan username atau email
        $request->validate([
            'siswa_nis' => 'required',
            'siswa_password' => 'required',
        ]);

        // 2. Cari siswa berdasarkan NIS (atau username/email)
        $siswa = Siswa::where('siswa_nis', $request->siswa_nis)->first();

        // 3. Cek siswa dan password
        // CATATAN: Sesuaikan 'siswa_password' dengan nama kolom password di tabel siswa
        if (!$siswa || !Hash::check($request->siswa_password, $siswa->siswa_password)) {
            return response()->json([
                'message' => 'NIS (username) atau password salah.'
            ], 401);
        }

        // 4. Jika berhasil, buat API Token (Sanctum)
        $token = $siswa->createToken('siswa-auth-token')->plainTextToken;

        // 5. Kirim response JSON yang berisi data siswa dan token
        return response()->json([
            'message' => 'Login siswa berhasil',
            'siswa'   => $siswa,
            'token'   => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Menangani permintaan logout siswa untuk API.
     * Rute ini HARUS dilindungi oleh middleware 'auth:sanctum'.
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan untuk otentikasi
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout siswa berhasil']);
    }
}
