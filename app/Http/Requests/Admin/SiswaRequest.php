<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Dipakai untuk tambah & ubah siswa (web dan API). */
class SiswaRequest extends FormRequest
{
    public function rules(): array
    {
        $siswa = $this->route('siswa');
        $isUpdate = $siswa !== null;

        return [
            'siswa_nama' => ['required', 'string', 'max:50'],
            'siswa_nis' => ['required', 'string', 'max:20', Rule::unique('siswas', 'siswa_nis')->ignore($siswa?->siswa_id, 'siswa_id')],
            'kelas_id' => ['required', 'integer', 'exists:kelas,kelas_id'],
            'siswa_email' => ['nullable', 'email', 'max:100', Rule::unique('siswas', 'siswa_email')->ignore($siswa?->siswa_id, 'siswa_id')],
            'siswa_hp' => ['nullable', 'string', 'max:20'],
            'siswa_alamat' => ['nullable', 'string', 'max:500'],
            'siswa_password' => [$isUpdate ? 'nullable' : 'required', Password::min(6)],
        ];
    }

    public function attributes(): array
    {
        return [
            'siswa_nama' => 'nama',
            'siswa_nis' => 'NIS',
            'kelas_id' => 'kelas',
            'siswa_email' => 'email',
            'siswa_hp' => 'nomor HP',
            'siswa_alamat' => 'alamat',
            'siswa_password' => 'password',
        ];
    }

    /** Data tervalidasi; password kosong saat update diabaikan. */
    public function payload(): array
    {
        return collect($this->validated())
            ->reject(fn ($value, $key) => $key === 'siswa_password' && blank($value))
            ->all();
    }
}
