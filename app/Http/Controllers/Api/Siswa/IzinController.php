<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Siswa\IzinRequest;
use App\Http\Resources\IzinResource;
use App\Models\Izin;
use App\Services\IzinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IzinController extends Controller
{
    public function __construct(private readonly IzinService $izinService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return IzinResource::collection($request->user()->izin()->latest()->paginate(20));
    }

    public function store(IzinRequest $request): JsonResponse
    {
        $izin = $this->izinService->ajukan($request->user(), $request->payload(), $request->file('bukti'));

        return IzinResource::make($izin)->response()->setStatusCode(201);
    }

    public function destroy(Request $request, Izin $izin): JsonResponse
    {
        abort_unless($izin->isOwnedBy($request->user()), 403);

        $this->izinService->batalkan($izin);

        return response()->json(['message' => 'Pengajuan izin dibatalkan.']);
    }
}
