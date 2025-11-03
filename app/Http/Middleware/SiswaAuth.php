<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SiswaAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('siswa')) {
            return redirect()->route('siswa.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
