<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProsesIzinRequest;
use App\Http\Resources\IzinResource;
use App\Models\Izin;
use App\Services\IzinService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IzinController extends Controller
{
    public function __construct(private readonly IzinService $izinService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return IzinResource::collection(
            Izin::with('siswa.kelas')->status($request->query('status'))->latest()->paginate(20)
        );
    }

    public function approve(ProsesIzinRequest $request, Izin $izin): IzinResource
    {
        return IzinResource::make($this->izinService->setujui($izin, $request->user(), $request->validated('catatan_admin')));
    }

    public function reject(ProsesIzinRequest $request, Izin $izin): IzinResource
    {
        return IzinResource::make($this->izinService->tolak($izin, $request->user(), $request->validated('catatan_admin')));
    }
}
