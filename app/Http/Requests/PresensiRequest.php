<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PresensiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required' => 'Lokasi Anda belum terdeteksi. Izinkan akses lokasi di browser.',
            'longitude.required' => 'Lokasi Anda belum terdeteksi. Izinkan akses lokasi di browser.',
        ];
    }
}
