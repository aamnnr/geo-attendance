<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Kelas; // <-- 1. PASTIKAN MODEL KELAS DI-IMPORT

class AdminJadwalController extends Controller
{
    /**
     * Menampilkan daftar semua jadwal, dikelompokkan per kelas.
     */
    public function index()
    {
        // --- INI ADALAH PERBAIKANNYA ---
        
        // 2. Definisikan daftar hari
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        // 3. Ambil SEMUA kelas, dan "Eager Load" relasi jadwal-nya
        // Ini akan mengambil semua kelas, lalu mengambil semua jadwal
        // yang terkait dengan kelas-kelas tersebut dalam 2 query.
        $kelas = Kelas::with('jadwal') // 'jadwal' adalah nama method relasi di Model Kelas
                        ->orderBy('kelas_nama', 'asc')
                        ->get();

        // 4. Kirimkan variabel $kelas dan $hariList ke view
        return view('admin.jadwal.index', compact('kelas', 'hariList'));
    }

    /**
     * Menampilkan form untuk menambah jadwal baru.
     */
    public function create()
    {
        // Ambil data kelas untuk dropdown, tambahkan 'request' untuk auto-select
        $kelas = Kelas::orderBy('kelas_nama', 'asc')->get(); 
        return view('admin.jadwal.create', compact('kelas'));
    }

    /**
     * Menyimpan jadwal baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kelas_id' => 'required|integer|exists:kelas,kelas_id',
            'mapel' => 'required|string|max:100',
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        Jadwal::create($validatedData);

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit jadwal.
     * $jadwal didapat dari Route-Model Binding (Jadwal $jadwal)
     */
    public function edit(Jadwal $jadwal) 
    {
        $kelas = Kelas::orderBy('kelas_nama', 'asc')->get();
        return view('admin.jadwal.edit', compact('jadwal', 'kelas'));
    }

    /**
     * Mengupdate jadwal di database.
     */
    public function update(Request $request, Jadwal $jadwal)
    {
        $validatedData = $request->validate([
            'kelas_id' => 'required|integer|exists:kelas,kelas_id',
            'mapel' => 'required|string|max:100',
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $jadwal->update($validatedData);

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Menghapus jadwal dari database.
     */
    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}

