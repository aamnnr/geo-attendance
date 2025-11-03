<?php

namespace App\Http\Controllers\Web\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa; // Import Siswa
use Illuminate\Support\Facades\Hash;

class SiswaProfileController extends Controller
{
    /**
     * Menampilkan halaman profil siswa.
     */
    public function index()
    {
        $siswaId = session('siswa.siswa_id');
        if (!$siswaId) {
            return redirect()->route('siswa.login')->with('error', 'Sesi Anda telah berakhir.');
        }

        // Ambil data siswa, termasuk relasi 'kelas'
        $siswa = Siswa::with('kelas')->find($siswaId);

        if (!$siswa) {
            return redirect()->route('siswa.login')->with('error', 'Data siswa tidak ditemukan.');
        }

        return view('siswa.profile', compact('siswa'));
    }

    /**
     * Memperbarui data profil siswa.
     */
    public function update(Request $request)
    {
        $siswaId = session('siswa.siswa_id');
        if (!$siswaId) {
            return redirect()->route('siswa.login')->with('error', 'Sesi Anda telah berakhir.');
        }

        $siswa = Siswa::find($siswaId);

        // Validasi
        $request->validate([
            'siswa_nama' => 'required|string|max:50',
            'siswa_email' => 'required|email|max:100|unique:siswas,siswa_email,' . $siswaId . ',siswa_id',
            'siswa_hp' => 'nullable|string|max:20',
            'siswa_alamat' => 'nullable|string',
            'password' => 'nullable|min:6|confirmed', // Untuk ganti password
        ]);

        // Kumpulkan data untuk di-update
        $data = $request->only('siswa_nama', 'siswa_email', 'siswa_hp', 'siswa_alamat');

        // Jika user memasukkan password baru
        if ($request->filled('password')) {
            $data['siswa_password'] = Hash::make($request->password);
        }

        // Update data
        $siswa->update($data);

        // Update session agar nama di navbar juga berubah
        session([
            'siswa.siswa_nama' => $siswa->siswa_nama,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
