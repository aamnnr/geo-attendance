<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Presensi</title>
    <!-- CSS Sederhana untuk PDF -->
    <style>
        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        h1 { font-size: 18px; }
        h3 { font-size: 14px; margin-top: 5px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer {
            margin-top: 30px;
            font-size: 9px;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>REKAP PRESENSI SISWA</h1>
        <h3>
            Periode: 
            @if($tanggal_mulai && $tanggal_sampai)
                {{ \Carbon\Carbon::parse($tanggal_mulai)->isoFormat('D MMM Y') }} s/d {{ \Carbon\Carbon::parse($tanggal_sampai)->isoFormat('D MMM Y') }}
            @else
                Keseluruhan
            @endif
        </h3>
        <h3>Kelas: {{ $kelas_nama }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Siswa</th>
                <th>NIS</th>
                <th>Kelas</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekaps as $index => $rekap)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($rekap->tanggal)->isoFormat('D MMM Y') }}</td>
                <td>{{ $rekap->siswa->siswa_nama ?? 'N/A' }}</td>
                <td>{{ $rekap->siswa->siswa_nis ?? 'N/A' }}</td>
                <td>{{ $rekap->siswa->kelas->kelas_nama ?? 'N/A' }}</td>
                <td class="text-center">{{ $rekap->jam_masuk ? \Carbon\Carbon::parse($rekap->jam_masuk)->format('H:i') : '-' }}</td>
                <td class="text-center">{{ $rekap->jam_keluar ? \Carbon\Carbon::parse($rekap->jam_keluar)->format('H:i') : '-' }}</td>
                <td class="text-center">{{ $rekap->status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ $tanggal_cetak }}
    </div>

</body>
</html>
