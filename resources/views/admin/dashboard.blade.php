@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Statistik Hari Ini -->
    <div class="col-md-3 mb-4">
        <div class="card text-center shadow border-0">
            <div class="card-header bg-success text-white">
                <i class="bi bi-person-check me-2"></i>Hadir Hari Ini
            </div>
            <div class="card-body">
                <h2 class="text-success">{{ $totalHadir ?? 0 }}</h2>
                <small class="text-muted">Siswa</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center shadow border-0">
            <div class="card-header bg-warning text-white">
                <i class="bi bi-envelope me-2"></i>Izin Hari Ini
            </div>
            <div class="card-body">
                <h2 class="text-warning">{{ $totalIzin ?? 0 }}</h2>
                <small class="text-muted">Siswa</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center shadow border-0">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-person-x me-2"></i>Alpha Hari Ini
            </div>
            <div class="card-body">
                <h2 class="text-danger">{{ $totalAlpha ?? 0 }}</h2>
                <small class="text-muted">Siswa</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center shadow border-0">
            <div class="card-header bg-info text-white">
                <i class="bi bi-people me-2"></i>Total Siswa
            </div>
            <div class="card-body">
                <h2 class="text-info">{{ $totalSiswa ?? 0 }}</h2>
                <small class="text-muted">Siswa</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Statistik Lainnya -->
    <div class="col-md-4 mb-4">
        <div class="card text-center shadow border-0">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-building me-2"></i>Total Kelas
            </div>
            <div class="card-body">
                <h2 class="text-primary">{{ $totalKelas ?? 0 }}</h2>
                <small class="text-muted">Kelas</small>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card text-center shadow border-0">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-clock-history me-2"></i>Izin Pending
            </div>
            <div class="card-body">
                <h2 class="text-secondary">{{ $izinPending ?? 0 }}</h2>
                <small class="text-muted">Menunggu</small>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card text-center shadow border-0">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-calendar-day me-2"></i>Hari Ini
            </div>
            <div class="card-body">
                <h4 class="text-dark">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Presensi Terbaru -->
@if(isset($presensiTerbaru) && $presensiTerbaru->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow border-0">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Presensi Terbaru Hari Ini
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jam Masuk</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($presensiTerbaru as $presensi)
                            <tr>
                                <td>{{ $presensi->siswa->siswa_nama ?? '-' }}</td>
                                <td>{{ $presensi->siswa->kelas->kelas_nama ?? '-' }}</td>
                                <td>{{ $presensi->jam_masuk ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-success">Hadir</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow border-0">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-4 text-muted"></i>
                <h5 class="mt-3 text-muted">Belum ada presensi hari ini</h5>
            </div>
        </div>
    </div>
</div>
@endif
@endsection