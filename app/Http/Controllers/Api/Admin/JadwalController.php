<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JadwalRequest;
use App\Http\Resources\JadwalResource;
use App\Models\Jadwal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JadwalController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return JadwalResource::collection(
            Jadwal::with('kelas')
                ->when($request->query('kelas_id'), fn ($q, $id) => $q->where('kelas_id', $id))
                ->when($request->query('hari'), fn ($q, $hari) => $q->where('hari', $hari))
                ->orderBy('kelas_id')
                ->orderBy('jam_mulai')
                ->get()
        );
    }

    public function store(JadwalRequest $request): JsonResponse
    {
        return JadwalResource::make(Jadwal::create($request->validated()))->response()->setStatusCode(201);
    }

    public function show(Jadwal $jadwal): JadwalResource
    {
        return JadwalResource::make($jadwal->load('kelas'));
    }

    public function update(JadwalRequest $request, Jadwal $jadwal): JadwalResource
    {
        $jadwal->update($request->validated());

        return JadwalResource::make($jadwal);
    }

    public function destroy(Jadwal $jadwal): JsonResponse
    {
        $jadwal->delete();

        return response()->json(['message' => 'Jadwal dihapus.']);
    }
}
