<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;

class ProfileApiController extends Controller
{
    /**
     * 📘 Mendapatkan profil siswa yang sedang login
     */
    public function me()
    {
        $user = Auth::guard('siswa')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $siswa = Siswa::with('kelas:id,nama_kelas')
                      ->select('id', 'nis', 'nama', 'kelas_id', 'alamat', 'no_hp', 'foto')
                      ->find($user->id);

        return response()->json([
            'status' => true,
            'data' => $siswa
        ], 200);
    }
}
