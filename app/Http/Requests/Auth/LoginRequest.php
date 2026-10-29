<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Login dengan guard tertentu + pembatasan percobaan (5x per menit).
 * Subclass cukup menentukan guard dan nama field identitas.
 */
abstract class LoginRequest extends FormRequest
{
    abstract protected function guard(): string;

    /** Nama field identitas di form. */
    abstract protected function identityField(): string;

    /** Nama kolom identitas di database. */
    abstract protected function identityColumn(): string;

    abstract protected function failedMessage(): string;

    public function rules(): array
    {
        return [
            $this->identityField() => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Validasi kredensial. Untuk web: login ke session. Untuk API: hanya cek
     * kredensial dan kembalikan user-nya supaya bisa dibuatkan token.
     *
     * @throws ValidationException
     */
    public function authenticate(bool $session = true): Authenticatable
    {
        $this->ensureIsNotRateLimited();

        $credentials = [
            $this->identityColumn() => $this->string($this->identityField())->toString(),
            'password' => $this->string('password')->toString(),
        ];

        $guard = Auth::guard($this->guard());
        $ok = $session
            ? $guard->attempt($credentials, $this->boolean('remember'))
            : $guard->validate($credentials);

        if (! $ok) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                $this->identityField() => $this->failedMessage(),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        return $session ? $guard->user() : $guard->getProvider()->retrieveByCredentials($credentials);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            $this->identityField() => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate($this->guard().'|'.Str::lower($this->string($this->identityField())).'|'.$this->ip());
    }
}
