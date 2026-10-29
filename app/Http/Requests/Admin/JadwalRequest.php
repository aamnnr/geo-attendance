<?php

namespace App\Http\Requests\Admin;

use App\Enums\Hari;
use App\Models\Jadwal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class JadwalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'kelas_id' => ['required', 'integer', 'exists:kelas,kelas_id'],
            'mapel' => ['required', 'string', 'max:100'],
            'guru' => ['nullable', 'string', 'max:100'],
            'hari' => ['required', Rule::enum(Hari::class)],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ];
    }

    public function attributes(): array
    {
        return [
            'kelas_id' => 'kelas',
            'mapel' => 'mata pelajaran',
            'jam_mulai' => 'jam mulai',
            'jam_selesai' => 'jam selesai',
        ];
    }

    /** Tolak jadwal yang bentrok dengan jadwal lain di kelas & hari yang sama. */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $bentrok = Jadwal::query()
                    ->where('kelas_id', $this->integer('kelas_id'))
                    ->where('hari', $this->input('hari'))
                    ->where('jam_mulai', '<', $this->input('jam_selesai'))
                    ->where('jam_selesai', '>', $this->input('jam_mulai'))
                    ->when($this->route('jadwal'), fn ($q, Jadwal $jadwal) => $q->whereKeyNot($jadwal->getKey()))
                    ->first();

                if ($bentrok) {
                    $validator->errors()->add('jam_mulai', "Bentrok dengan {$bentrok->mapel} ({$bentrok->jamMulai()}–{$bentrok->jamSelesai()}).");
                }
            },
        ];
    }
}
