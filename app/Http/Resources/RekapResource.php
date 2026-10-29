<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Rekap */
class RekapResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->rekap_id,
            'tanggal' => $this->tanggal->toDateString(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'jam_masuk' => $this->jamMasuk(),
            'jam_keluar' => $this->jamKeluar(),
            'jarak' => $this->jarak,
            'keterangan' => $this->keterangan,
            'siswa' => SiswaResource::make($this->whenLoaded('siswa')),
        ];
    }
}
