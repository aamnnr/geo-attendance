@extends('layouts.app')

@section('title', 'Edit Rekap')

@section('page-title', 'Edit Rekap Presensi')

@section('content')
<div class="card shadow border-0">
    <div class="card-body">
        <form action="{{ route('admin.rekap.update', $rekap->rekap_id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="siswa_id" class="form-label">Siswa *</label>
                    <select class="form-control @error('siswa_id') is-invalid @enderror" 
                            id="siswa_id" name="siswa_id" required>
                        <option value="">Pilih Siswa</option>
                        @foreach($siswas as $siswa)
                            <option value="{{ $siswa->siswa_id }}" {{ old('siswa_id', $rekap->siswa_id) == $siswa->siswa_id ? 'selected' : '' }}>
                                {{ $siswa->siswa_nama }} - {{ $siswa->kelas->kelas_nama ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('siswa_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tanggal" class="form-label">Tanggal *</label>
                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                           id="tanggal" name="tanggal" value="{{ old('tanggal', $rekap->tanggal) }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="jam_masuk" class="form-label">Jam Masuk</label>
                    <input type="time" class="form-control @error('jam_masuk') is-invalid @enderror" 
                           id="jam_masuk" name="jam_masuk" value="{{ old('jam_masuk', $rekap->jam_masuk) }}">
                    @error('jam_masuk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="jam_keluar" class="form-label">Jam Keluar</label>
                    <input type="time" class="form-control @error('jam_keluar') is-invalid @enderror" 
                           id="jam_keluar" name="jam_keluar" value="{{ old('jam_keluar', $rekap->jam_keluar) }}">
                    @error('jam_keluar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status *</label>
                <select class="form-control @error('status') is-invalid @enderror" 
                        id="status" name="status" required>
                    <option value="Hadir" {{ old('status', $rekap->status) == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="Izin" {{ old('status', $rekap->status) == 'Izin' ? 'selected' : '' }}>Izin</option>
                    <option value="Alpha" {{ old('status', $rekap->status) == 'Alpha' ? 'selected' : '' }}>Alpha</option>
                    <option value="Sakit" {{ old('status', $rekap->status) == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.rekap.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Update Rekap</button>
            </div>
        </form>
    </div>
</div>
@endsection