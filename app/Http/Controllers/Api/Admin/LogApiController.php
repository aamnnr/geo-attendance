<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;

class LogApiController extends Controller
{
    /**
     * Get system logs
     */
    public function index(Request $request)
    {
        $query = Log::query();
        
        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->orderBy('created_at', 'desc')
                     ->limit(100) // Limit to last 100 logs
                     ->get();

        return response()->json([
            'status' => 'success',
            'data' => $logs
        ]);
    }
}