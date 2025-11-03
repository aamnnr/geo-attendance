<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminSiswaController extends Controller
{
    public function index()
    {
        $siswas = Siswa::with('kelas')->get();
        return view('admin.siswa.index', compact('siswas'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        return view('admin.siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_nama' => 'required|string|max:50',
            'siswa_nis' => 'required|unique:siswas,siswa_nis',
            'kelas_id' => 'required|exists:kelas,kelas_id',
            'siswa_password' => 'required|min:6',
        ]);

        // Hash password sebelum disimpan
        $validated['siswa_password'] = Hash::make($request->siswa_password);

        Siswa::create($validated);
        return redirect()->route('admin.siswa.index')->with('success', '✅ Siswa berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        $kelas = Kelas::all();
        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        
        $validated = $request->validate([
            'siswa_nama' => 'required|string|max:50',
            'siswa_nis' => 'required|unique:siswas,siswa_nis,' . $siswa->siswa_id . ',siswa_id',
            'kelas_id' => 'required|exists:kelas,kelas_id',
            'siswa_password' => 'nullable|min:6',
        ]);

        // Update password jika diisi
        if ($request->filled('siswa_password')) {
            $validated['siswa_password'] = Hash::make($request->siswa_password);
        } else {
            unset($validated['siswa_password']);
        }

        $siswa->update($validated);
        return redirect()->route('admin.siswa.index')->with('success', '✏️ Data siswa berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();
        return redirect()->route('admin.siswa.index')->with('success', '🗑️ Siswa berhasil dihapus!');
    }
}