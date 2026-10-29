<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiswaRequest;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Services\StatistikService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaController extends Controller
{
    public function index(Request $request): View
    {
        $siswas = Siswa::with('kelas')
            ->search($request->query('q'))
            ->when($request->query('kelas_id'), fn ($q, $kelasId) => $q->where('kelas_id', $kelasId))
            ->orderBy('siswa_nama')
            ->paginate(15)
            ->withQueryString();

        return view('admin.siswa.index', [
            'siswas' => $siswas,
            'kelasList' => Kelas::orderBy('kelas_nama')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.siswa.form', [
            'siswa' => new Siswa,
            'kelasList' => Kelas::orderBy('kelas_nama')->get(),
        ]);
    }

    public function store(SiswaRequest $request): RedirectResponse
    {
        $siswa = Siswa::create($request->payload());

        return redirect()->route('admin.siswa.index')->with('success', "Siswa {$siswa->siswa_nama} berhasil ditambahkan.");
    }

    public function show(Siswa $siswa, StatistikService $statistik): View
    {
        $siswa->load('kelas');

        return view('admin.siswa.show', [
            'siswa' => $siswa,
            'ringkasan' => $statistik->perStatus($siswa->rekap()->getQuery()),
            'rekaps' => $siswa->rekap()->latest('tanggal')->paginate(10),
            'izins' => $siswa->izin()->latest()->limit(5)->get(),
        ]);
    }

    public function edit(Siswa $siswa): View
    {
        return view('admin.siswa.form', [
            'siswa' => $siswa,
            'kelasList' => Kelas::orderBy('kelas_nama')->get(),
        ]);
    }

    public function update(SiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        $siswa->update($request->payload());

        return redirect()->route('admin.siswa.index')->with('success', "Data {$siswa->siswa_nama} berhasil diperbarui.");
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        $siswa->delete();

        return redirect()->route('admin.siswa.index')->with('success', "Siswa {$siswa->siswa_nama} telah dihapus.");
    }
}
