<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $logs = ActivityLog::query()
            ->when($request->query('tipe'), fn ($q, $tipe) => $q->where('tipe', $tipe))
            ->when($request->query('tanggal'), fn ($q, $tgl) => $q->whereDate('created_at', $tgl))
            ->latest()
            ->limit(100)
            ->get(['id', 'tipe', 'pesan', 'created_at']);

        return response()->json(['data' => $logs]);
    }
}
