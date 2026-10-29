<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusIzin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProsesIzinRequest;
use App\Http\Requests\Siswa\IzinRequest;
use App\Models\Izin;
use App\Models\Siswa;
use App\Services\IzinService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IzinController extends Controller
{
    public function __construct(private readonly IzinService $izinService) {}

    public function index(Request $request): View
    {
        // ?status=semua (atau nilai tak dikenal) menampilkan semua izin.
        $status = StatusIzin::tryFrom($request->query('status', StatusIzin::Pending->value));

        $izins = Izin::with(['siswa.kelas', 'admin'])
            ->status($status)
            ->when($request->query('q'), fn ($q, $term) => $q->whereHas('siswa', fn ($s) => $s->search($term)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $jumlah = Izin::toBase()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.izin.index', [
            'izins' => $izins,
            'statusAktif' => $status,
            'jumlah' => $jumlah,
            'siswaList' => Siswa::with('kelas')->orderBy('siswa_nama')->get(['siswa_id', 'siswa_nama', 'siswa_nis', 'kelas_id']),
        ]);
    }

    /** Admin mencatat izin atas nama siswa (langsung disetujui). */
    public function store(IzinRequest $request): RedirectResponse
    {
        $izin = $this->izinService->catatOlehAdmin($request->user('admin'), $request->payload(), $request->file('bukti'));

        return redirect()->route('admin.izin.index', ['status' => StatusIzin::Disetujui->value])
            ->with('success', "Izin untuk {$izin->siswa->siswa_nama} berhasil dicatat.");
    }

    public function approve(ProsesIzinRequest $request, Izin $izin): RedirectResponse
    {
        $this->izinService->setujui($izin, $request->user('admin'), $request->validated('catatan_admin'));

        return back()->with('success', "Izin {$izin->siswa->siswa_nama} disetujui.");
    }

    public function reject(ProsesIzinRequest $request, Izin $izin): RedirectResponse
    {
        $this->izinService->tolak($izin, $request->user('admin'), $request->validated('catatan_admin'));

        return back()->with('success', "Izin {$izin->siswa->siswa_nama} ditolak.");
    }
}
