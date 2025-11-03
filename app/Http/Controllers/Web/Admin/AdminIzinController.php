<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Izin;
use App\Models\Rekap;

class AdminIzinController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan izin.
     */
    public function index()
    {
        // PERBAIKAN: Tambahkan 'admin' ke eager loading
        $izins = Izin::with(['siswa.kelas', 'admin']) 
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                    
        return view('admin.izin', compact('izins'));
    }

    /**
     * Menyetujui pengajuan izin.
     */
// ... sisa method approve() Anda sudah benar ...
    public function approve(Request $request, Izin $izin)
    {
        // 1. Cek apakah izin masih 'pending'
        if ($izin->status != 'pending') {
            return back()->with('error', 'Izin ini sudah diproses sebelumnya.');
        }

        // 2. Ubah status izin menjadi 'disetujui'
        $izin->update([
            'status' => 'disetujui',
            'admin_id' => session('admin.admin_id') // Catat admin yang memproses
        ]);

        // 3. (OPSIONAL TAPI PENTING)
        // Buat rekap 'Izin' untuk siswa pada rentang tanggal tersebut
        $currentDate = \Carbon\Carbon::parse($izin->izin_dari);
        $endDate = \Carbon\Carbon::parse($izin->izin_sampai);

        while ($currentDate->lte($endDate)) {
            Rekap::updateOrCreate(
                [
                    'siswa_id' => $izin->siswa_id,
                    'tanggal' => $currentDate->toDateString(),
                ],
                [
                    'status' => 'Izin',
                    'jam_masuk' => null, // Set null karena Izin
                    'jam_keluar' => null,
                ]
            );
            $currentDate->addDay();
        }

        return back()->with('success', 'Izin telah disetujui dan rekap telah dibuat.');
    }

    /**
     * Menolak pengajuan izin.
     */
// ... sisa method reject() Anda sudah benar ...
    public function reject(Request $request, Izin $izin)
    {
        // 1. Cek apakah izin masih 'pending'
        if ($izin->status != 'pending') {
            return back()->with('error', 'Izin ini sudah diproses sebelumnya.');
        }

        // 2. Ubah status izin menjadi 'ditolak'
        $izin->update([
            'status' => 'ditolak',
            'admin_id' => session('admin.admin_id') // Catat admin yang memproses
        ]);

        return back()->with('success', 'Izin telah ditolak.');
    }
}

