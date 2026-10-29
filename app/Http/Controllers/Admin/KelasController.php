<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KelasRequest;
use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(): View
    {
        $kelas = Kelas::withCount(['siswa', 'jadwal'])->orderBy('kelas_nama')->get();

        return view('admin.kelas.index', compact('kelas'));
    }

    public function store(KelasRequest $request): RedirectResponse
    {
        $kelas = Kelas::create($request->validated());

        return back()->with('success', "Kelas {$kelas->kelas_nama} berhasil ditambahkan.");
    }

    public function update(KelasRequest $request, Kelas $kelas): RedirectResponse
    {
        $kelas->update($request->validated());

        return back()->with('success', "Kelas {$kelas->kelas_nama} berhasil diperbarui.");
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        if ($kelas->siswa()->exists()) {
            return back()->with('error', "Kelas {$kelas->kelas_nama} masih memiliki siswa. Pindahkan siswanya terlebih dahulu.");
        }

        $kelas->delete();

        return back()->with('success', "Kelas {$kelas->kelas_nama} telah dihapus.");
    }
}
