<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KelasRequest;
use App\Http\Resources\KelasResource;
use App\Models\Kelas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KelasController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return KelasResource::collection(Kelas::withCount(['siswa', 'jadwal'])->orderBy('kelas_nama')->get());
    }

    public function store(KelasRequest $request): JsonResponse
    {
        return KelasResource::make(Kelas::create($request->validated()))->response()->setStatusCode(201);
    }

    public function show(Kelas $kelas): KelasResource
    {
        return KelasResource::make($kelas->loadCount(['siswa', 'jadwal']));
    }

    public function update(KelasRequest $request, Kelas $kelas): KelasResource
    {
        $kelas->update($request->validated());

        return KelasResource::make($kelas);
    }

    public function destroy(Kelas $kelas): JsonResponse
    {
        if ($kelas->siswa()->exists()) {
            return response()->json(['message' => 'Kelas masih memiliki siswa.'], 409);
        }

        $kelas->delete();

        return response()->json(['message' => 'Kelas dihapus.']);
    }
}
