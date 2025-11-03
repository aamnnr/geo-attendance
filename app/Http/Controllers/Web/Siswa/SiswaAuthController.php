<?php

namespace App\Http\Controllers\Web\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa; // Pastikan Model Siswa di-import
use Illuminate\Support\Facades\Hash; // Pastikan Hash di-import

class SiswaAuthController extends Controller
{
    public function showLoginForm()
    {
        // Tampilkan form login dari 'resources/views/auth/login-siswa.blade.php'
        return view('auth.login-siswa');
    }

    public function login(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'nis' => 'required',
            'password' => 'required',
        ]);

        // 2. Cari siswa berdasarkan NIS
        $siswa = Siswa::where('siswa_nis', $request->nis)->first();

        // 3. Cek apakah siswa ada DAN password-nya cocok
        if ($siswa && Hash::check($request->password, $siswa->getAuthPassword())) {
            
            // 4. Buat data session manual
            // INI BAGIAN PENTING YANG MEMPERBAIKI MASALAH ANDA
            $siswaData = [
                'siswa_id' => $siswa->siswa_id,
                'siswa_nis' => $siswa->siswa_nis,
                'siswa_nama' => $siswa->siswa_nama,
                'kelas_id' => $siswa->kelas_id, // <-- PASTIKAN INI DITAMBAHKAN
            ];

            // 5. Simpan data siswa ke session
            session(['siswa' => $siswaData]);

            // 6. Regenerasi session untuk keamanan
            $request->session()->regenerate();

            // 7. Arahkan ke dashboard siswa
            return redirect()->route('siswa.dashboard')->with('success', 'Login berhasil!');
        }

        // 8. Jika gagal, kembali ke login dengan error
        return back()->with('error', 'NIS atau password salah.');
    }

    public function logout(Request $request)
    {
        // Hapus session 'siswa'
        $request->session()->forget('siswa');
        
        // Hapus semua data session dan buat token baru
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('siswa.login')->with('success', 'Logout berhasil!');
    }
}

