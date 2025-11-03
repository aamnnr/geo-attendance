<?php

namespace App\Exports;

use App\Models\Rekap;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

/**
 * File ini menangani logika untuk ekspor Excel.
 * Dibuat untuk AdminRekapController.
 */
class RekapExport implements FromCollection, WithHeadings, WithMapping
{
    protected $rekaps;

    /**
     * Terima data rekap yang sudah difilter dari controller
     */
    public function __construct(Collection $rekaps)
    {
        $this->rekaps = $rekaps;
    }

    /**
     * Mengembalikan data yang akan diekspor
     */
    public function collection()
    {
        return $this->rekaps;
    }

    /**
     * Menentukan judul (header) untuk setiap kolom di Excel
     */
    public function headings(): array
    {
        return [
            'Tanggal',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Jam Masuk',
            'Jam Keluar',
            'Status',
        ];
    }

    /**
     * Memetakan data dari $rekap ke format array untuk setiap baris
     */
    public function map($rekap): array
    {
        return [
            Carbon::parse($rekap->tanggal)->isoFormat('D MMM Y'),
            $rekap->siswa->siswa_nis ?? 'N/A',
            $rekap->siswa->siswa_nama ?? 'Siswa Dihapus',
            $rekap->siswa->kelas->kelas_nama ?? 'Kelas Dihapus',
            $rekap->jam_masuk ? Carbon::parse($rekap->jam_masuk)->format('H:i:s') : '-',
            $rekap->jam_keluar ? Carbon::parse($rekap->jam_keluar)->format('H:i:s') : '-',
            $rekap->status,
        ];
    }
}
