<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Siswa\ProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('siswa.profile', [
            'siswa' => $request->user('siswa')->load('kelas'),
        ]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        $request->user('siswa')->update($request->payload());

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
