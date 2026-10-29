<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LiburRequest;
use App\Models\Libur;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LiburController extends Controller
{
    public function index(): View
    {
        return view('admin.libur.index', [
            'liburs' => Libur::orderByDesc('libur_dari')->paginate(15),
        ]);
    }

    public function store(LiburRequest $request): RedirectResponse
    {
        Libur::create($request->validated());

        return back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function update(LiburRequest $request, Libur $libur): RedirectResponse
    {
        $libur->update($request->validated());

        return back()->with('success', 'Hari libur berhasil diperbarui.');
    }

    public function destroy(Libur $libur): RedirectResponse
    {
        $libur->delete();

        return back()->with('success', 'Hari libur telah dihapus.');
    }
}
