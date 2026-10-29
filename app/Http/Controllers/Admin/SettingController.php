<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings', ['settings' => Setting::values()]);
    }

    public function update(SettingRequest $request): RedirectResponse
    {
        Setting::put($request->validated());

        ActivityLog::record('pengaturan', "{$request->user('admin')->admin_nama} memperbarui pengaturan presensi.");

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
