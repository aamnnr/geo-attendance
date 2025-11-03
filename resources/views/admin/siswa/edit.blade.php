@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('page-title', 'Edit Data Siswa')

@section('content')
<div class="card shadow border-0">
    <div class="card-body">
        <form action="{{ route('admin.siswa.update', $siswa->siswa_id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="siswa_nama" class="form-label">Nama Siswa *</label>
                    <input type="text" class="form-control @error('siswa_nama') is-invalid @enderror" 
                           id="siswa_nama" name="siswa_nama" value="{{ old('siswa_nama', $siswa->siswa_nama) }}" required>
                    @error('siswa_nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="siswa_nis" class="form-label">NIS *</label>
                    <input type="text" class="form-control @error('siswa_nis') is-invalid @enderror" 
                           id="siswa_nis" name="siswa_nis" value="{{ old('siswa_nis', $siswa->siswa_nis) }}" required>
                    @error('siswa_nis')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="kelas_id" class="form-label">Kelas *</label>
                    <select class="form-control @error('kelas_id') is-invalid @enderror" 
                            id="kelas_id" name="kelas_id" required>
                        <option value="">Pilih Kelas</option>
                        @foreach($kelas as $item)
                            <option value="{{ $item->kelas_id }}" {{ old('kelas_id', $siswa->kelas_id) == $item->kelas_id ? 'selected' : '' }}>
                                {{ $item->kelas_nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('kelas_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="siswa_password" class="form-label">Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" class="form-control @error('siswa_password') is-invalid @enderror" 
                           id="siswa_password" name="siswa_password">
                    @error('siswa_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Minimal 6 karakter</small>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Update Siswa</button>
            </div>
        </form>
    </div>
</div>
@endsection