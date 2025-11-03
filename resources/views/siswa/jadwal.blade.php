@extends('layouts.siswa')

@section('title', 'Jadwal Siswa')

@section('content')
<div class="card shadow">
    <div class="card-header bg-primary text-white">Jadwal Kelas</div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Mata Pelajaran</th>
                    <th>Jam</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jadwals as $jadwal)
                <tr>
                    <td>{{ $jadwal->hari }}</td>
                    <td>{{ $jadwal->mapel }}</td>
                    <td>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
