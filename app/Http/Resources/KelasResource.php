<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Kelas */
class KelasResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->kelas_id,
            'nama' => $this->kelas_nama,
            'wali_kelas' => $this->wali_kelas,
            'jumlah_siswa' => $this->whenCounted('siswa'),
            'jumlah_jadwal' => $this->whenCounted('jadwal'),
        ];
    }
}
