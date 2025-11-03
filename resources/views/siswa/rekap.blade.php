@extends('layouts.siswa')

@section('title', 'Rekap Presensi')

@section('content')
<div class="card shadow">
    <div class="card-header bg-primary text-white">Rekap Kehadiran</div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekaps as $rekap)
                <tr>
                    <td>{{ $rekap->tanggal }}</td>
                    <td>{{ $rekap->jam_masuk ?? '-' }}</td>
                    <td>{{ $rekap->jam_keluar ?? '-' }}</td>
                    <td>{{ $rekap->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
