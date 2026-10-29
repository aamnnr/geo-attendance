<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RekapExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RekapRequest;
use App\Models\Kelas;
use App\Models\Rekap;
use App\Models\Siswa;
use App\Services\StatistikService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RekapController extends Controller
{
    private const FILTERS = ['kelas_id', 'status', 'tanggal_mulai', 'tanggal_sampai', 'q'];

    public function index(Request $request, StatistikService $statistik): View
    {
        $filters = $request->only(self::FILTERS);

        return view('admin.rekap.index', [
            'rekaps' => $this->query($filters)->paginate(20)->withQueryString(),
            // Ringkasan dihitung tanpa filter status supaya tab status tetap menampilkan jumlahnya.
            'ringkasan' => $statistik->perStatus(Rekap::filter(collect($filters)->except('status')->all())),
            'filters' => $filters,
            'kelasList' => Kelas::orderBy('kelas_nama')->get(),
            'siswaList' => Siswa::with('kelas')->orderBy('siswa_nama')->get(['siswa_id', 'siswa_nama', 'siswa_nis', 'kelas_id']),
        ]);
    }

    public function store(RekapRequest $request): RedirectResponse
    {
        Rekap::create($request->validated());

        return back()->with('success', 'Rekap presensi berhasil ditambahkan.');
    }

    public function update(RekapRequest $request, Rekap $rekap): RedirectResponse
    {
        $rekap->update($request->validated());

        return back()->with('success', 'Rekap presensi berhasil diperbarui.');
    }

    public function destroy(Rekap $rekap): RedirectResponse
    {
        $rekap->delete();

        return back()->with('success', 'Rekap presensi telah dihapus.');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $rekaps = $this->query($request->only(self::FILTERS))->get();

        return Excel::download(new RekapExport($rekaps), $this->namaFile('xlsx'));
    }

    public function exportPdf(Request $request): Response
    {
        $filters = $request->only(self::FILTERS);

        return Pdf::loadView('admin.rekap.pdf', [
            'rekaps' => $this->query($filters)->get(),
            'kelas' => isset($filters['kelas_id']) ? Kelas::find($filters['kelas_id']) : null,
            'filters' => $filters,
        ])->setPaper('a4', 'landscape')->download($this->namaFile('pdf'));
    }

    private function query(array $filters): Builder
    {
        return Rekap::with('siswa.kelas')
            ->filter($filters)
            ->orderByDesc('tanggal')
            ->orderBy(Siswa::select('siswa_nama')->whereColumn('siswas.siswa_id', 'rekaps.siswa_id'));
    }

    private function namaFile(string $ext): string
    {
        return 'rekap-presensi-'.now()->format('Ymd-His').'.'.$ext;
    }
}
