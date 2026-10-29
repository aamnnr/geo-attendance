<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Hari;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JadwalRequest;
use App\Models\Jadwal;
use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function index(Request $request): View
    {
        $kelasList = Kelas::orderBy('kelas_nama')->get();
        $kelasAktif = $kelasList->firstWhere('kelas_id', $request->integer('kelas_id')) ?? $kelasList->first();

        $jadwalPerHari = $kelasAktif
            ? $kelasAktif->jadwal()->orderBy('jam_mulai')->get()->groupBy(fn (Jadwal $j) => $j->hari->value)
            : collect();

        return view('admin.jadwal.index', [
            'kelasList' => $kelasList,
            'kelasAktif' => $kelasAktif,
            'jadwalPerHari' => $jadwalPerHari,
            'hariList' => Hari::cases(),
        ]);
    }

    public function store(JadwalRequest $request): RedirectResponse
    {
        $jadwal = Jadwal::create($request->validated());

        return redirect()->route('admin.jadwal.index', ['kelas_id' => $jadwal->kelas_id])
            ->with('success', "Jadwal {$jadwal->mapel} berhasil ditambahkan.");
    }

    public function update(JadwalRequest $request, Jadwal $jadwal): RedirectResponse
    {
        $jadwal->update($request->validated());

        return redirect()->route('admin.jadwal.index', ['kelas_id' => $jadwal->kelas_id])
            ->with('success', "Jadwal {$jadwal->mapel} berhasil diperbarui.");
    }

    public function destroy(Jadwal $jadwal): RedirectResponse
    {
        $jadwal->delete();

        return back()->with('success', "Jadwal {$jadwal->mapel} telah dihapus.");
    }
}
