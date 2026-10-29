<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Siswa\ProfileRequest;
use App\Http\Resources\SiswaResource;

class ProfileController extends Controller
{
    public function update(ProfileRequest $request): SiswaResource
    {
        $request->user()->update($request->payload());

        return SiswaResource::make($request->user()->load('kelas'));
    }
}
