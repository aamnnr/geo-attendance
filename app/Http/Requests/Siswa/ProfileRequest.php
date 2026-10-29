<?php

namespace App\Http\Requests\Siswa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileRequest extends FormRequest
{
    public function rules(): array
    {
        $siswa = $this->user('siswa') ?? $this->user();

        return [
            'siswa_email' => ['nullable', 'email', 'max:100', Rule::unique('siswas', 'siswa_email')->ignore($siswa->siswa_id, 'siswa_id')],
            'siswa_hp' => ['nullable', 'string', 'max:20'],
            'siswa_alamat' => ['nullable', 'string', 'max:500'],
            'current_password' => ['nullable', 'required_with:password', 'current_password:'.($this->user('siswa') ? 'siswa' : 'sanctum')],
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ];
    }

    public function attributes(): array
    {
        return [
            'siswa_email' => 'email',
            'siswa_hp' => 'nomor HP',
            'siswa_alamat' => 'alamat',
            'current_password' => 'password saat ini',
        ];
    }

    public function payload(): array
    {
        $data = $this->safe()->only(['siswa_email', 'siswa_hp', 'siswa_alamat']);

        if ($this->filled('password')) {
            $data['siswa_password'] = $this->input('password');
        }

        return $data;
    }
}
