<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProsesIzinRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'catatan_admin' => ['nullable', 'string', 'max:255'],
        ];
    }
}
