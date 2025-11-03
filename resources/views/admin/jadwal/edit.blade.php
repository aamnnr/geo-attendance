@extends('layouts.app')

@section('title', 'Edit Jadwal')
@section('page-title', 'Edit Jadwal')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body">
        
        <!-- ======================================================= -->
        <!-- INI ADALAH BLOK UNTUK MENAMPILKAN ERROR VALIDASI         -->
        <!-- ======================================================= -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <h5 class="alert-heading">Gagal menyimpan!</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- ======================================================= -->

        <form action="{{ route('admin.jadwal.update', $jadwal->jadwal_id) }}" method="POST">
            @csrf
            @method('PUT') <!-- PENTING: Method PUT untuk update -->
            
            <div class="mb-3">
                <label for="kelas_id" class="form-label fw-semibold">Kelas</label>
                <select name="kelas_id" id="kelas_id" class="form-select @error('kelas_id') is-invalid @enderror">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->kelas_id }}" {{ old('kelas_id', $jadwal->kelas_id) == $k->kelas_id ? 'selected' : '' }}>
                            {{ $k->kelas_nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="mapel" class="form-label fw-semibold">Mata Pelajaran</label>
                <input type="text" name="mapel" id="mapel" class="form-control @error('mapel') is-invalid @enderror" value="{{ old('mapel', $jadwal->mapel) }}">
            </div>

            <div class="mb-3">
                <label for="hari" class="form-label fw-semibold">Hari</label>
                <select name="hari" id="hari" class="form-select @error('hari') is-invalid @enderror">
                    <option value="">-- Pilih Hari --</option>
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                        <option value="{{ $hari }}" {{ old('hari', $jadwal->hari) == $hari ? 'selected' : '' }}>
                            {{ $hari }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="jam_mulai" class="form-label fw-semibold">Jam Mulai</label>
                    <input type="time" name="jam_mulai" id="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror" value="{{ old('jam_mulai', $jadwal->jam_mulai) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="jam_selesai" class="form-label fw-semibold">Jam Selesai</label>
                    <input type="time" name="jam_selesai" id="jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror" value="{{ old('jam_selesai', $jadwal->jam_selesai) }}">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-2"></i> Update Jadwal
            </button>
            <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary">
                Batal
            </a>
        </form>
    </div>
</div>
@endsection
