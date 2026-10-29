<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RekapRequest;
use App\Http\Resources\RekapResource;
use App\Models\Rekap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RekapController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return RekapResource::collection(
            Rekap::with('siswa.kelas')
                ->filter($request->only(['kelas_id', 'siswa_id', 'status', 'tanggal_mulai', 'tanggal_sampai', 'q']))
                ->orderByDesc('tanggal')
                ->paginate(min($request->integer('per_page', 50), 200))
        );
    }

    public function store(RekapRequest $request): JsonResponse
    {
        return RekapResource::make(Rekap::create($request->validated()))->response()->setStatusCode(201);
    }

    public function show(Rekap $rekap): RekapResource
    {
        return RekapResource::make($rekap->load('siswa.kelas'));
    }

    public function update(RekapRequest $request, Rekap $rekap): RekapResource
    {
        $rekap->update($request->validated());

        return RekapResource::make($rekap);
    }

    public function destroy(Rekap $rekap): JsonResponse
    {
        $rekap->delete();

        return response()->json(['message' => 'Rekap dihapus.']);
    }
}
