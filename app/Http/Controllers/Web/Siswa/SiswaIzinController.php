<?php

namespace App\Http\Controllers\Web\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Izin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage; // <-- 1. Import Storage

class SiswaIzinController extends Controller
{
    /**
     * Menampilkan halaman pengajuan izin dan riwayat izin.
     */
    public function index()
    {
        $siswaId = session('siswa.siswa_id');
        if (!$siswaId) {
            return redirect()->route('siswa.login')->with('error', 'Sesi Anda telah berakhir.');
        }

        $riwayatIzin = Izin::where('siswa_id', $siswaId)
                            ->orderBy('created_at', 'desc')
                            ->paginate(5); 

        return view('siswa.izin', [
            'riwayatIzin' => $riwayatIzin
        ]);
    }

    /**
     * Menyimpan pengajuan izin baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'izin_dari' => 'required|date',
            'izin_sampai' => 'required|date|after_or_equal:izin_dari',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Izinkan pdf
        ]);

        $siswaId = session('siswa.siswa_id');
        if (!$siswaId) {
            return redirect()->route('siswa.login')->with('error', 'Sesi Anda telah berakhir.');
        }

        $path = null;
        // 2. Logika untuk menyimpan file bukti
        if ($request->hasFile('bukti')) {
            // Pastikan Anda sudah menjalankan `php artisan storage:link`
            $path = $request->file('bukti')->store('bukti_izin', 'public');
        }

        Izin::create([
            'siswa_id' => $siswaId,
            'keterangan' => $request->keterangan,
            'izin_dari' => $request->izin_dari,
            'izin_sampai' => $request->izin_sampai,
            'bukti' => $path,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pengajuan izin berhasil dikirim.');
    }

    /**
     * Membatalkan (menghapus) pengajuan izin yang masih pending.
     */
    public function destroy(Request $request, Izin $izin)
    {
        $siswaId = session('siswa.siswa_id');

        // 1. Cek Keamanan: Apakah izin ini milik siswa yang login?
        if ($izin->siswa_id != $siswaId) {
            return back()->with('error', 'Anda tidak berhak mengakses ini.');
        }

        // 2. Cek Status: Hanya izin 'pending' yang bisa dibatalkan
        if ($izin->status != 'pending') {
            return back()->with('error', 'Izin yang sudah diproses tidak dapat dibatalkan.');
        }

        // 4. Hapus file bukti jika ada
        if ($izin->bukti) {
            Storage::disk('public')->delete($izin->bukti);
        }

        // 5. Hapus data izin dari database
        $izin->delete();

        return back()->with('success', 'Pengajuan izin telah berhasil dibatalkan.');
    }
}

