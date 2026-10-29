<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SiswaLoginRequest;
use App\Http\Resources\SiswaResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(SiswaLoginRequest $request): JsonResponse
    {
        $siswa = $request->authenticate(session: false);

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $siswa->createToken('siswa-api', ['siswa'])->plainTextToken,
            'token_type' => 'Bearer',
            'siswa' => SiswaResource::make($siswa->load('kelas')),
        ]);
    }

    public function me(Request $request): SiswaResource
    {
        return SiswaResource::make($request->user()->load('kelas'));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }
}
