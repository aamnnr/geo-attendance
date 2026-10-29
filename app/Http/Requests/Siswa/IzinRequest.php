<?php

namespace App\Http\Requests\Siswa;

use App\Enums\JenisIzin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Pengajuan izin. Dipakai siswa (web & API) dan admin (dengan field siswa_id).
 */
class IzinRequest extends FormRequest
{
    public function rules(): array
    {
        $olehAdmin = $this->routeIs('admin.*', 'api.admin.*');

        return [
            'siswa_id' => [Rule::requiredIf($olehAdmin), Rule::excludeIf(! $olehAdmin), 'integer', 'exists:siswas,siswa_id'],
            'jenis' => ['required', Rule::enum(JenisIzin::class)],
            'keterangan' => ['required', 'string', 'max:255'],
            'izin_dari' => ['required', 'date', $olehAdmin ? 'date' : 'after_or_equal:today'],
            'izin_sampai' => ['required', 'date', 'after_or_equal:izin_dari'],
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'catatan_admin' => [Rule::excludeIf(! $olehAdmin), 'nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'siswa_id' => 'siswa',
            'izin_dari' => 'tanggal mulai',
            'izin_sampai' => 'tanggal selesai',
            'bukti' => 'file bukti',
        ];
    }

    /** Data izin tanpa file (file diproses terpisah). */
    public function payload(): array
    {
        return collect($this->validated())->except('bukti')->all();
    }
}
