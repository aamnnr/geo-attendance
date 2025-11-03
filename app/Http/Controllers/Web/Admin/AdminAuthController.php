<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin; // TAMBAHKAN
use Illuminate\Support\Facades\Hash; // TAMBAHKAN

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        // Pastikan view Anda ada di: resources/views/auth/login.blade.php
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'admin_username' => 'required',
            'admin_password' => 'required',
        ]);

        // 2. Cari admin
        $admin = Admin::where('admin_username', $request->admin_username)->first();

        // 3. Cek admin dan password
        // Pastikan kolom password adalah 'admin_password'
        if (!$admin || !Hash::check($request->admin_password, $admin->admin_password)) {
            return back()->withErrors([
                'admin_username' => 'Username atau password salah.',
            ])->withInput($request->only('admin_username'));
        }

        // 4. BERHASIL! Atur session custom
        // Ini adalah session yang diperiksa oleh middleware AdminAuth
        session(['admin' => [
            'admin_id' => $admin->admin_id,
            'admin_nama' => $admin->admin_nama,
            'admin_username' => $admin->admin_username,
        ]]);

        // 5. Regenerate session dan redirect
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')->with('success', 'Login berhasil!');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        // Hapus session custom 'admin'
        $request->session()->forget('admin');

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Arahkan ke halaman utama setelah logout
        return redirect()->route('home')->with('success', 'Logout berhasil!');
    }
}

