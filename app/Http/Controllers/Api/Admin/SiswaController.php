<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiswaRequest;
use App\Http\Resources\SiswaResource;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SiswaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return SiswaResource::collection(
            Siswa::with('kelas')
                ->search($request->query('q'))
                ->when($request->query('kelas_id'), fn ($q, $id) => $q->where('kelas_id', $id))
                ->orderBy('siswa_nama')
                ->paginate(min($request->integer('per_page', 20), 100))
        );
    }

    public function store(SiswaRequest $request): JsonResponse
    {
        $siswa = Siswa::create($request->payload())->load('kelas');

        return SiswaResource::make($siswa)->response()->setStatusCode(201);
    }

    public function show(Siswa $siswa): SiswaResource
    {
        return SiswaResource::make($siswa->load('kelas'));
    }

    public function update(SiswaRequest $request, Siswa $siswa): SiswaResource
    {
        $siswa->update($request->payload());

        return SiswaResource::make($siswa->load('kelas'));
    }

    public function destroy(Siswa $siswa): JsonResponse
    {
        $siswa->delete();

        return response()->json(['message' => 'Siswa dihapus.']);
    }
}
