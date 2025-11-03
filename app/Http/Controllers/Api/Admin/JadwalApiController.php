<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class JadwalApiController extends Controller
{
    public function index()
    {
        return response()->json(Jadwal::all());
    }

    public function show($id)
    {
        return response()->json(Jadwal::findOrFail($id));
    }

    public function store(Request $request)
    {
        $jadwal = Jadwal::create($request->all());
        return response()->json($jadwal, 201);
    }

    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->update($request->all());
        return response()->json($jadwal);
    }

    public function destroy($id)
    {
        Jadwal::destroy($id);
        return response()->json(['message' => 'Jadwal dihapus']);
    }
}
