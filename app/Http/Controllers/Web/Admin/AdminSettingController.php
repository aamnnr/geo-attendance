<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting; // Import model Setting

class AdminSettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan.
     * Akan mengambil pengaturan dari DB, atau membuatnya jika belum ada.
     */
    public function index()
    {
        // Ambil pengaturan, atau buat dengan nilai default jika tidak ada
        $settings = [
            'school_lat' => Setting::firstOrCreate(
                ['key' => 'school_lat'],
                ['value' => '-6.2088'] // Nilai default (ganti jika perlu)
            ),
            'school_lng' => Setting::firstOrCreate(
                ['key' => 'school_lng'],
                ['value' => '106.8456'] // Nilai default
            ),
            'school_radius' => Setting::firstOrCreate(
                ['key' => 'school_radius'],
                ['value' => '100'] // Nilai default (dalam meter)
            ),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Menyimpan pembaruan pengaturan.
     */
    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'school_lat' => 'required|numeric',
            'school_lng' => 'required|numeric',
            'school_radius' => 'required|integer|min:10',
        ]);

        // Simpan atau perbarui setiap pengaturan
        Setting::updateOrCreate(
            ['key' => 'school_lat'],
            ['value' => $request->school_lat]
        );

        Setting::updateOrCreate(
            ['key' => 'school_lng'],
            ['value' => $request->school_lng]
        );

        Setting::updateOrCreate(
            ['key' => 'school_radius'],
            ['value' => $request->school_radius]
        );

        return back()->with('success', 'Pengaturan lokasi presensi berhasil diperbarui.');
    }
}

