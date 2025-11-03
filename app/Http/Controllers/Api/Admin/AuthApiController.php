<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin; // <-- Tambahkan ini
use Illuminate\Support\Facades\Hash; // <-- Tambahkan ini
use Illuminate\Validation\ValidationException; 

class AuthApiController extends Controller
{
    /**
     * Menangani permintaan login admin untuk API.
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'admin_username' => 'required',
            'admin_password' => 'required',
        ]);

        // 2. Cari admin berdasarkan username
        $admin = Admin::where('admin_username', $request->admin_username)->first();

        // 3. Cek admin dan password
        if (!$admin || !Hash::check($request->admin_password, $admin->admin_password)) {
            // Jika gagal, kirim response error 401 (Unauthorized)
            return response()->json([
                'message' => 'Username atau password salah.'
            ], 401);
        }

        // 4. Jika berhasil, buat API Token (Sanctum)
        $token = $admin->createToken('admin-auth-token')->plainTextToken;

        // 5. Kirim response JSON yang berisi data admin dan token
        return response()->json([
            'message' => 'Login admin berhasil',
            'admin'   => $admin,
            'token'   => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Menangani permintaan logout admin untuk API.
     * Rute ini HARUS dilindungi oleh middleware 'auth:sanctum'.
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan untuk otentikasi
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout admin berhasil']);
    }
}
