<?php

namespace App\Http\Requests\Auth;

class AdminLoginRequest extends LoginRequest
{
    protected function guard(): string
    {
        return 'admin';
    }

    protected function identityField(): string
    {
        return 'username';
    }

    protected function identityColumn(): string
    {
        return 'admin_username';
    }

    protected function failedMessage(): string
    {
        return 'Username atau password salah.';
    }
}
