<?php

namespace App\Http\Requests\Auth;

class SiswaLoginRequest extends LoginRequest
{
    protected function guard(): string
    {
        return 'siswa';
    }

    protected function identityField(): string
    {
        return 'nis';
    }

    protected function identityColumn(): string
    {
        return 'siswa_nis';
    }

    protected function failedMessage(): string
    {
        return 'NIS atau password salah.';
    }
}
