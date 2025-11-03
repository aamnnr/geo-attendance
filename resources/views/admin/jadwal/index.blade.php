@extends('layouts.app')

@section('title', 'Data Jadwal')
@section('page-title', 'Jadwal Pelajaran')

@section('page-actions')
    <a href="{{ route('admin.jadwal.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Tambah Jadwal
    </a>
@endsection

@section('content')

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow border-0">
    <div class="card-body">
        @if(isset($kelas) && $kelas->count() > 0)
            <!-- Tab untuk Pilih Kelas -->
            <ul class="nav nav-tabs mb-4" id="kelasTab" role="tablist">
                @foreach($kelas as $index => $kelasItem)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                            id="tab-{{ $kelasItem->kelas_id }}" 
                            data-bs-toggle="tab" 
                            data-bs-target="#content-{{ $kelasItem->kelas_id }}" 
                            type="button" 
                            role="tab">
                        <i class="bi bi-building me-1"></i>
                        {{ $kelasItem->kelas_nama }}
                        <!-- Pastikan relasi 'jadwal' ada di model Kelas -->
                        <span class="badge bg-secondary ms-1">{{ $kelasItem->jadwal->count() }}</span>
                    </button>
                </li>
                @endforeach
            </ul>

            <!-- Content untuk setiap kelas -->
            <div class="tab-content" id="kelasTabContent">
                @foreach($kelas as $index => $kelasItem)
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                     id="content-{{ $kelasItem->kelas_id }}" 
                     role="tabpanel">
                    
                    <!-- Accordion untuk hari -->
                    <div class="accordion" id="accordion-{{ $kelasItem->kelas_id }}">
                        @php
                            $jadwalByHari = $kelasItem->jadwal->groupBy('hari');
                        @endphp
                        
                        @foreach($hariList as $hariIndex => $hari)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $hariIndex === 0 ? '' : 'collapsed' }}" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse-{{ $kelasItem->kelas_id }}-{{ $hariIndex }}"
                                        aria-expanded="{{ $hariIndex === 0 ? 'true' : 'false' }}">
                                    <i class="bi bi-calendar-day me-2"></i>
                                    <strong>{{ $hari }}</strong>
                                    <span class="badge bg-primary ms-2">
                                        {{ isset($jadwalByHari[$hari]) ? $jadwalByHari[$hari]->count() : 0 }} Mapel
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse-{{ $kelasItem->kelas_id }}-{{ $hariIndex }}" 
                                 class="accordion-collapse collapse {{ $hariIndex === 0 ? 'show' : '' }}"
                                 data-bs-parent="#accordion-{{ $kelasItem->kelas_id }}">
                                <div class="accordion-body p-3">
                                    @if(isset($jadwalByHari[$hari]) && $jadwalByHari[$hari]->count() > 0)
                                        <div class="row g-2">
                                            <!-- PERBAIKAN: sortBy('jam_mulai') -->
                                            @foreach($jadwalByHari[$hari]->sortBy('jam_mulai') as $jadwal)
                                            <div class="col-12">
                                                <div class="card border-0 bg-light">
                                                    <div class="card-body py-2">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-4">
                                                                <strong class="text-primary">{{ $jadwal->mapel }}</strong>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <small class="text-muted">
                                                                    <i class="bi bi-clock me-1"></i>
                                                                    <!-- PERBAIKAN: jam_mulai dan jam_selesai -->
                                                                    {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                                </small>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <small class="text-muted">
                                                                    @php
                                                                        try {
                                                                            // PERBAIKAN: jam_mulai dan jam_selesai
                                                                            $start = \Carbon\Carbon::parse($jadwal->jam_mulai);
                                                                            $end = \Carbon\Carbon::parse($jadwal->jam_selesai);
                                                                            $diff = $start->diff($end);
                                                                            
                                                                            if ($diff->h > 0 && $diff->i > 0) {
                                                                                echo $diff->h . ' jam ' . $diff->i . ' menit';
                                                                            } elseif ($diff->h > 0) {
                                                                                echo $diff->h . ' jam';
                                                                            } else {
                                                                                echo $diff->i . ' menit';
                                                                            }
                                                                        } catch (\Exception $e) {
                                                                            echo 'N/A';
                                                                        }
                                                                    @endphp
                                                                </small>
                                                            </div>
                                                            <div class="col-md-2 text-end">
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="{{ route('admin.jadwal.edit', $jadwal->jadwal_id) }}" 
                                                                       class="btn btn-outline-warning btn-sm">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </a>
                                                                    <form action="{{ route('admin.jadwal.destroy', $jadwal->jadwal_id) }}" 
                                                                          method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" 
                                                                                class="btn btn-outline-danger btn-sm" 
                                                                                onclick="return confirm('Hapus jadwal {{ $jadwal->mapel }}?')">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-3">
                                            <i class="bi bi-calendar-x text-muted me-2"></i>
                                            <span class="text-muted">Belum ada jadwal untuk hari {{ $hari }}</span>
                                            <a href="{{ route('admin.jadwal.create') }}?kelas={{ $kelasItem->kelas_id }}&hari={{ $hari }}" 
                                               class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="bi bi-plus-circle me-1"></i>Tambah
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                </div>
                @endforeach
            </div>

        @else
            <!-- Fallback: Tampilan jika tidak ada data -->
            <div class="text-center py-5">
                <i class="bi bi-calendar display-4 text-muted"></i>
                <h5 class="text-muted mt-3">Belum ada data jadwal</h5>
                <a href="{{ route('admin.jadwal.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Jadwal Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
