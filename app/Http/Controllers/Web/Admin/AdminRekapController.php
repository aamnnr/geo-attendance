<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rekap;
use App\Models\Kelas;
use App\Models\Siswa;
use Carbon\Carbon;

// --- PERBAIKAN: Mengaktifkan Use Statements ---
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapExport; // File ini akan kita buat
// --- SELESAI ---

class AdminRekapController extends Controller
{
    /**
     * Menampilkan halaman rekap presensi dengan filter.
     */
    public function index(Request $request)
    {
        // 1. Ambil data untuk filter
        $kelas_list = Kelas::orderBy('kelas_nama', 'asc')->get();

        // 2. Ambil data rekap yang sudah difilter
        // Gunakan getFilteredRekap() agar logikanya terpusat
        $query = $this->getFilteredRekap($request);

        // 3. Paginate hasil query
        // withQueryString() penting agar filter tetap ada saat pindah halaman
        $rekaps = $query->paginate(20)->withQueryString();

        return view('admin.rekap.index', compact('rekaps', 'kelas_list'));
    }

    /**
     * Fungsi helper privat untuk mengambil data rekap yang sudah difilter
     * (Digunakan oleh index, exportExcel, dan exportPdf)
     */
    private function getFilteredRekap(Request $request)
    {
        // Mulai query dengan eager loading (untuk view)
        $query = Rekap::with('siswa.kelas')
                    
                    // --- PERBAIKAN DI SINI ---
                    // 1. JOIN tabel 'siswas' agar bisa digunakan untuk sorting
                    ->join('siswas', 'rekaps.siswa_id', '=', 'siswas.siswa_id')
                    
                    // 2. SELECT 'rekaps.*' agar tidak bentrok kolom (cth: created_at)
                    ->select('rekaps.*')
                    
                    // 3. Perjelas 'tanggal' milik tabel 'rekaps'
                    ->orderBy('rekaps.tanggal', 'desc')
                    
                    // 4. Gunakan nama tabel 'siswas.siswa_nama' untuk sorting
                    ->orderBy('siswas.siswa_nama', 'asc');
                    // --- SELESAI ---

        // Filter berdasarkan Kelas
        if ($request->filled('kelas_id')) {
            // whereHas akan otomatis membuat sub-query/join yang benar
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // Filter berdasarkan Tanggal Mulai
        if ($request->filled('tanggal_mulai')) {
            $query->where('rekaps.tanggal', '>=', $request->tanggal_mulai);
        }

        // Filter berdasarkan Tanggal Selesai
        if ($request->filled('tanggal_sampai')) {
            $query->where('rekaps.tanggal', '<=', $request->tanggal_sampai);
        }

        return $query;
    }

    /**
     * Menangani ekspor data ke Excel.
     * MEMBUTUHKAN: 'maatwebsite/excel'
     */
    public function exportExcel(Request $request)
    {
        // Ambil data yang sudah difilter (tanpa pagination)
        $rekaps = $this->getFilteredRekap($request)->get();
        $filename = 'rekap_presensi_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        // --- PERBAIKAN: Mengaktifkan Ekspor Excel ---
        return Excel::download(new RekapExport($rekaps), $filename);
        // --- SELESAI ---
    }

    /**
     * Menangani ekspor data ke PDF.
     * MEMBUTUHKAN: 'barryvdh/laravel-dompdf'
     */
    public function exportPdf(Request $request)
    {
        // Ambil data yang sudah difilter (tanpa pagination)
        $rekaps = $this->getFilteredRekap($request)->get();
        $filename = 'rekap_presensi_' . Carbon::now()->format('Ymd_His') . '.pdf';

        // Data untuk dikirim ke view PDF
        $data = [
            'rekaps' => $rekaps,
            'kelas_nama' => $request->filled('kelas_id') ? Kelas::find($request->kelas_id)->kelas_nama : 'Semua Kelas',
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_sampai' => $request->tanggal_sampai,
            'tanggal_cetak' => Carbon::now()->isoFormat('D MMMM YYYY'),
        ];
        
        // --- PERBAIKAN: Mengaktifkan Ekspor PDF ---
        $pdf = Pdf::loadView('admin.rekap.print', $data);
        $pdf->setPaper('a4', 'landscape'); // Atur ke landscape
        return $pdf->download($filename);
        // --- SELESAI ---
    }

    public function create()
    {
        $siswas = Siswa::with('kelas')->get();
        return view('admin.rekap.create', compact('siswas')); // ✅ GANTI: rekap.create → admin.rekap.create
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,siswa_id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_keluar' => 'nullable|date_format:H:i|after_or_equal:jam_masuk',
            'status' => 'required|string|max:20',
        ]);

        Rekap::create($validated);
        return redirect()->route('admin.rekap.index')->with('success', '✅ Rekap absensi berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $rekap = Rekap::findOrFail($id);
        $siswas = Siswa::with('kelas')->get();
        return view('admin.rekap.edit', compact('rekap', 'siswas')); // ✅ GANTI: rekap.edit → admin.rekap.edit
    }

    public function update(Request $request, $id)
    {
        $rekap = Rekap::findOrFail($id);
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,siswa_id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_keluar' => 'nullable|date_format:H:i|after_or_equal:jam_masuk',
            'status' => 'required|string|max:20',
        ]);

        $rekap->update($validated);
        return redirect()->route('admin.rekap.index')->with('success', '✏️ Rekap absensi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $rekap = Rekap::findOrFail($id);
        $rekap->delete();
        return redirect()->route('admin.rekap.index')->with('success', '🗑️ Rekap berhasil dihapus!');
    }
}