<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\PresensiRequest;
use App\Http\Resources\JadwalResource;
use App\Http\Resources\RekapResource;
use App\Services\PresensiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function __construct(private readonly PresensiService $presensi) {}

    /** Kondisi presensi hari ini: aksi berikutnya (masuk/pulang/selesai/...). */
    public function show(Request $request): JsonResponse
    {
        $kondisi = $this->presensi->kondisiHariIni($request->user());

        return response()->json(['data' => [
            'tanggal' => $kondisi['tanggal']->toDateString(),
            'aksi' => $kondisi['aksi'],
            'pesan' => $kondisi['pesan'],
            'rekap' => $kondisi['rekap'] ? RekapResource::make($kondisi['rekap']) : null,
            'jadwal' => JadwalResource::collection($kondisi['jadwal']),
        ]]);
    }

    /** PresensiException dirender sebagai JSON 422 di bootstrap/app.php. */
    public function store(PresensiRequest $request): JsonResponse
    {
        $hasil = $this->presensi->catat($request->user(), $request->float('latitude'), $request->float('longitude'));

        return response()->json([
            'message' => $hasil['pesan'],
            'tipe' => $hasil['tipe'],
            'data' => RekapResource::make($hasil['rekap']),
        ], $hasil['tipe'] === 'masuk' ? 201 : 200);
    }
}
