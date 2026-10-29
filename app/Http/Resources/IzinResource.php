<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Izin */
class IzinResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->izin_id,
            'jenis' => $this->jenis->value,
            'jenis_label' => $this->jenis->label(),
            'keterangan' => $this->keterangan,
            'izin_dari' => $this->izin_dari->toDateString(),
            'izin_sampai' => $this->izin_sampai->toDateString(),
            'durasi' => $this->durasi,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'bukti_url' => $this->bukti_url,
            'catatan_admin' => $this->catatan_admin,
            'diproses_pada' => $this->diproses_pada?->toIso8601String(),
            'diajukan_pada' => $this->created_at?->toIso8601String(),
            'siswa' => SiswaResource::make($this->whenLoaded('siswa')),
        ];
    }
}
