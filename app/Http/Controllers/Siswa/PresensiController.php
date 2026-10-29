<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\PresensiRequest;
use App\Models\Setting;
use App\Services\PresensiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PresensiController extends Controller
{
    public function __construct(private readonly PresensiService $presensi) {}

    public function create(Request $request): View
    {
        return view('siswa.presensi', [
            'kondisi' => $this->presensi->kondisiHariIni($request->user('siswa')),
            'sekolah' => [
                'nama' => Setting::get('school_name'),
                'lat' => (float) Setting::get('school_lat'),
                'lng' => (float) Setting::get('school_lng'),
                'radius' => (int) Setting::get('school_radius'),
            ],
        ]);
    }

    /** PresensiException ditangani terpusat di bootstrap/app.php. */
    public function store(PresensiRequest $request): RedirectResponse
    {
        $hasil = $this->presensi->catat(
            $request->user('siswa'),
            $request->float('latitude'),
            $request->float('longitude'),
        );

        return redirect()->route('siswa.dashboard')->with('success', $hasil['pesan']);
    }
}
