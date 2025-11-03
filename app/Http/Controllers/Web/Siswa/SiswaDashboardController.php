<?php

namespace App\Http\Controllers\Web\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Rekap;

class SiswaDashboardController extends Controller
{
    public function index()
    {
        $rekap = Rekap::where('siswa_id', auth('siswa')->id())->latest()->get();
        return view('siswa.dashboard', compact('rekap'));
    }
}
