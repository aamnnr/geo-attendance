<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Izin;
use App\Models\Rekap;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class IzinApiController extends Controller
{
    /**
     * Get all izin requests
     */
    public function index()
    {
        $izin = Izin::with('siswa.kelas')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $izin
        ]);
    }

    /**
     * Update izin status
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak'
        ]);

        $izin = Izin::findOrFail($id);
        $izin->status = $request->status;
        $izin->admin_id = Auth::id();
        $izin->save();

        // Jika disetujui, buat rekap izin
        if ($request->status == 'disetujui') {
            $this->createRekapIzin($izin);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Status izin berhasil diperbarui.',
            'data' => $izin
        ]);
    }

    /**
     * Approve izin
     */
    public function approve($id)
    {
        $izin = Izin::findOrFail($id);
        $izin->status = 'disetujui';
        $izin->admin_id = Auth::id();
        $izin->save();

        $this->createRekapIzin($izin);

        return response()->json([
            'status' => 'success',
            'message' => 'Izin berhasil disetujui.',
            'data' => $izin
        ]);
    }

    /**
     * Reject izin
     */
    public function reject($id)
    {
        $izin = Izin::findOrFail($id);
        $izin->status = 'ditolak';
        $izin->admin_id = Auth::id();
        $izin->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Izin berhasil ditolak.',
            'data' => $izin
        ]);
    }

    /**
     * Create rekap records for approved izin
     */
    private function createRekapIzin(Izin $izin)
    {
        $start = Carbon::parse($izin->izin_dari);
        $end = Carbon::parse($izin->izin_sampai);

        for ($date = $start; $date->lte($end); $date->addDay()) {
            // Skip weekends if needed
            // if ($date->isWeekend()) continue;

            $exists = Rekap::where('siswa_id', $izin->siswa_id)
                ->whereDate('tanggal', $date)
                ->exists();

            if (!$exists) {
                Rekap::create([
                    'siswa_id' => $izin->siswa_id,
                    'tanggal' => $date->toDateString(),
                    'status' => 'Izin',
                    'jam_masuk' => null,
                    'jam_keluar' => null,
                ]);
            }
        }
    }
}