<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Siswa */
class SiswaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->siswa_id,
            'nis' => $this->siswa_nis,
            'nama' => $this->siswa_nama,
            'email' => $this->siswa_email,
            'hp' => $this->siswa_hp,
            'alamat' => $this->siswa_alamat,
            'kelas' => KelasResource::make($this->whenLoaded('kelas')),
        ];
    }
}
