<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LiburRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'keterangan' => ['required', 'string', 'max:100'],
            'libur_dari' => ['required', 'date'],
            'libur_sampai' => ['required', 'date', 'after_or_equal:libur_dari'],
        ];
    }

    public function attributes(): array
    {
        return ['libur_dari' => 'tanggal mulai', 'libur_sampai' => 'tanggal selesai'];
    }
}
