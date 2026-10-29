<?php

namespace App\Exports;

use App\Models\Rekap;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /** @param  Collection<int, Rekap>  $rekaps  sudah di-eager load dengan siswa.kelas */
    public function __construct(private readonly Collection $rekaps) {}

    public function collection(): Collection
    {
        return $this->rekaps;
    }

    public function headings(): array
    {
        return ['Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Jam Masuk', 'Jam Keluar', 'Status', 'Keterangan'];
    }

    /** @param  Rekap  $rekap */
    public function map($rekap): array
    {
        return [
            $rekap->tanggal->translatedFormat('d M Y'),
            $rekap->siswa?->siswa_nis ?? '-',
            $rekap->siswa?->siswa_nama ?? 'Siswa dihapus',
            $rekap->siswa?->kelas?->kelas_nama ?? '-',
            $rekap->jamMasuk() ?? '-',
            $rekap->jamKeluar() ?? '-',
            $rekap->status->label(),
            $rekap->keterangan ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
