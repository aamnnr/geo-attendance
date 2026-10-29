<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'school_name' => ['required', 'string', 'max:100'],
            'school_lat' => ['required', 'numeric', 'between:-90,90'],
            'school_lng' => ['required', 'numeric', 'between:-180,180'],
            'school_radius' => ['required', 'integer', 'between:10,5000'],
            'toleransi_terlambat' => ['required', 'integer', 'between:0,120'],
            'batas_presensi_masuk' => ['required', 'date_format:H:i'],
        ];
    }

    public function attributes(): array
    {
        return [
            'school_name' => 'nama sekolah',
            'school_lat' => 'latitude',
            'school_lng' => 'longitude',
            'school_radius' => 'radius',
            'toleransi_terlambat' => 'toleransi terlambat',
            'batas_presensi_masuk' => 'batas presensi masuk',
        ];
    }
}
