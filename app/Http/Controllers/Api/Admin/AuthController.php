<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(AdminLoginRequest $request): JsonResponse
    {
        $admin = $request->authenticate(session: false);

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $admin->createToken('admin-api', ['admin'])->plainTextToken,
            'token_type' => 'Bearer',
            'admin' => $admin->only('admin_id', 'admin_username', 'admin_nama'),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->only('admin_id', 'admin_username', 'admin_nama')]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }
}
