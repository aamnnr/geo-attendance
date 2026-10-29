<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Presensi</title>
    <style>
        @page { margin: 28px 32px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #26262b; }
        .header { border-bottom: 3px solid #f5b622; padding-bottom: 10px; margin-bottom: 14px; }
        h1 { font-size: 18px; margin: 0 0 2px; }
        .muted { color: #6f6f78; }
        .meta td { padding: 1px 12px 1px 0; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #f7f7f8; text-align: left; font-weight: bold; padding: 6px 7px; border-bottom: 1px solid #e2e2e6; }
        table.data td { padding: 5px 7px; border-bottom: 1px solid #eeeef0; }
        .badge { padding: 1px 6px; border-radius: 8px; font-size: 9px; }
        .hadir { background: #ecfdf5; color: #047857; }
        .terlambat { background: #fffbeb; color: #b45309; }
        .izin { background: #f0f9ff; color: #0369a1; }
        .sakit { background: #f5f3ff; color: #6d28d9; }
        .alpa { background: #fef2f2; color: #b91c1c; }
        .footer { position: fixed; bottom: -10px; left: 0; right: 0; font-size: 8px; color: #9a9aa3; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekap Presensi Siswa</h1>
        <div class="muted">{{ \App\Models\Setting::get('school_name') }}</div>
        <table class="meta" style="margin-top: 8px">
            <tr>
                <td class="muted">Kelas</td><td>{{ $kelas?->kelas_nama ?? 'Semua kelas' }}</td>
                <td class="muted">Periode</td>
                <td>
                    {{ isset($filters['tanggal_mulai']) ? \Illuminate\Support\Carbon::parse($filters['tanggal_mulai'])->translatedFormat('j M Y') : 'Awal' }}
                    – {{ isset($filters['tanggal_sampai']) ? \Illuminate\Support\Carbon::parse($filters['tanggal_sampai'])->translatedFormat('j M Y') : 'Sekarang' }}
                </td>
                <td class="muted">Total</td><td>{{ $rekaps->count() }} data</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 24px">No</th>
                <th>Tanggal</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rekaps as $rekap)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $rekap->tanggal->translatedFormat('d M Y') }}</td>
                    <td>{{ $rekap->siswa?->siswa_nis }}</td>
                    <td>{{ $rekap->siswa?->siswa_nama }}</td>
                    <td>{{ $rekap->siswa?->kelas?->kelas_nama }}</td>
                    <td>{{ $rekap->jamMasuk() ?? '-' }}</td>
                    <td>{{ $rekap->jamKeluar() ?? '-' }}</td>
                    <td><span class="badge {{ $rekap->status->value }}">{{ $rekap->status->label() }}</span></td>
                    <td>{{ $rekap->keterangan }}</td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align: center; padding: 20px" class="muted">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak {{ now()->translatedFormat('j F Y, H:i') }} · {{ config('app.name') }}</div>
</body>
</html>
