<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusPresensi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RekapRequest extends FormRequest
{
    public function rules(): array
    {
        $rekap = $this->route('rekap');

        return [
            'siswa_id' => [
                'required', 'integer', 'exists:siswas,siswa_id',
                Rule::unique('rekaps', 'siswa_id')
                    ->where('tanggal', $this->input('tanggal'))
                    ->ignore($rekap?->rekap_id, 'rekap_id'),
            ],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['required', Rule::enum(StatusPresensi::class)],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_keluar' => ['nullable', 'date_format:H:i', 'after_or_equal:jam_masuk'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'siswa_id.unique' => 'Siswa ini sudah memiliki rekap pada tanggal tersebut.',
        ];
    }

    public function attributes(): array
    {
        return ['siswa_id' => 'siswa', 'jam_masuk' => 'jam masuk', 'jam_keluar' => 'jam keluar'];
    }
}
