<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Siswa\IzinRequest;
use App\Models\Izin;
use App\Services\IzinService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IzinController extends Controller
{
    public function __construct(private readonly IzinService $izinService) {}

    public function index(Request $request): View
    {
        return view('siswa.izin', [
            'izins' => $request->user('siswa')->izin()->with('admin')->latest()->paginate(10),
        ]);
    }

    public function store(IzinRequest $request): RedirectResponse
    {
        $this->izinService->ajukan($request->user('siswa'), $request->payload(), $request->file('bukti'));

        return back()->with('success', 'Pengajuan izin terkirim. Tunggu persetujuan admin.');
    }

    public function destroy(Request $request, Izin $izin): RedirectResponse
    {
        abort_unless($izin->isOwnedBy($request->user('siswa')), 403);

        $this->izinService->batalkan($izin);

        return back()->with('success', 'Pengajuan izin dibatalkan.');
    }
}
