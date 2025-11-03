<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasApiController extends Controller
{
    public function index()
    {
        return response()->json(Kelas::all());
    }

    public function show($id)
    {
        return response()->json(Kelas::findOrFail($id));
    }

    public function store(Request $request)
    {
        $kelas = Kelas::create($request->all());
        return response()->json($kelas, 201);
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->all());
        return response()->json($kelas);
    }

    public function destroy($id)
    {
        Kelas::destroy($id);
        return response()->json(['message' => 'Kelas dihapus']);
    }
}
