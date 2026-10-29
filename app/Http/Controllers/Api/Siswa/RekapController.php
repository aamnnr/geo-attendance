<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Resources\RekapResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RekapController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        return RekapResource::collection(
            $request->user()->rekap()
                ->filter($request->only(['status', 'tanggal_mulai', 'tanggal_sampai']))
                ->latest('tanggal')
                ->paginate(30)
        );
    }
}
