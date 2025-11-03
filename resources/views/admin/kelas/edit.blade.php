@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('page-title', 'Edit Data Kelas')

@section('content')
<div class="card shadow border-0">
    <div class="card-body">
        <form action="{{ route('admin.kelas.update', $kelas->kelas_id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="kelas_nama" class="form-label">Nama Kelas *</label>
                <input type="text" class="form-control @error('kelas_nama') is-invalid @enderror" 
                       id="kelas_nama" name="kelas_nama" 
                       value="{{ old('kelas_nama', $kelas->kelas_nama) }}" required>
                @error('kelas_nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Update Kelas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection