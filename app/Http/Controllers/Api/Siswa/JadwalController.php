<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Resources\JadwalResource;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JadwalController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $jadwal = Jadwal::where('kelas_id', $request->user()->kelas_id)
            ->orderBy('jam_mulai')
            ->get()
            ->sortBy(fn (Jadwal $j) => $j->hari->urutan())
            ->values();

        return JadwalResource::collection($jadwal);
    }
}
