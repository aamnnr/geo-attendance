<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class AdminKelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::all();
        return view('admin.kelas.index', compact('kelas')); // 
    }

    public function create()
    {
        return view('admin.kelas.create'); // 
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_nama' => 'required|string|max:100|unique:kelas,kelas_nama',
        ]);

        Kelas::create($validated);
        return redirect()->route('admin.kelas.index')->with('success', '✅ Kelas berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        return view('admin.kelas.edit', compact('kelas'));
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $validated = $request->validate([
            'kelas_nama' => 'required|string|max:100|unique:kelas,kelas_nama,' . $kelas->kelas_id . ',kelas_id',
        ]);

        $kelas->update($validated);
        return redirect()->route('admin.kelas.index')->with('success', '✏️ Data kelas berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();
        return redirect()->route('admin.kelas.index')->with('success', '🗑️ Kelas berhasil dihapus!');
    }
}