<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Jadwal */
class JadwalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->jadwal_id,
            'kelas_id' => $this->kelas_id,
            'mapel' => $this->mapel,
            'guru' => $this->guru,
            'hari' => $this->hari->value,
            'jam_mulai' => $this->jamMulai(),
            'jam_selesai' => $this->jamSelesai(),
            'kelas' => KelasResource::make($this->whenLoaded('kelas')),
        ];
    }
}
