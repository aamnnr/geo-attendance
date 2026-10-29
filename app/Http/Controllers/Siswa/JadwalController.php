<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\Hari;
use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function __invoke(Request $request): View
    {
        $siswa = $request->user('siswa')->load('kelas');

        return view('siswa.jadwal', [
            'kelas' => $siswa->kelas,
            'jadwalPerHari' => $siswa->kelas->jadwal()->orderBy('jam_mulai')->get()
                ->groupBy(fn (Jadwal $j) => $j->hari->value),
            'hariList' => Hari::cases(),
            'hariIni' => Hari::fromDate(now()),
        ]);
    }
}
