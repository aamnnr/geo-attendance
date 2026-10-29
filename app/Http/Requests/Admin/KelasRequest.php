<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KelasRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'kelas_nama' => ['required', 'string', 'max:50', Rule::unique('kelas', 'kelas_nama')->ignore($this->route('kelas')?->kelas_id, 'kelas_id')],
            'wali_kelas' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return ['kelas_nama' => 'nama kelas', 'wali_kelas' => 'wali kelas'];
    }
}
