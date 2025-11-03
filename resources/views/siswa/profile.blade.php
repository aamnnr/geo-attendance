@extends('layouts.siswa')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Edit Profil</h5>
            </div>
            <div class="card-body">
                
                <!-- Tampilkan error validasi -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('siswa.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT') <!-- Gunakan method PUT untuk update -->

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="siswa_nama" class="form-control" value="{{ old('siswa_nama', $siswa->siswa_nama) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">NIS</label>
                        <input type="text" class="form-control" value="{{ $siswa->siswa_nis }}" readonly disabled>
                        <div class="form-text">NIS tidak dapat diubah.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kelas</label>
                        <input type="text" class="form-control" value="{{ $siswa->kelas->kelas_nama ?? 'N/A' }}" readonly disabled>
                    </div>

                    <hr>
                    <h5 class="h6 text-primary">Informasi Kontak</h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="siswa_email" class="form-control" value="{{ old('siswa_email', $siswa->siswa_email) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. HP</label>
                        <input type="text" name="siswa_hp" class="form-control" value="{{ old('siswa_hp', $siswa->siswa_hp) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="siswa_alamat" class="form-control" rows="3">{{ old('siswa_alamat', $siswa->siswa_alamat) }}</textarea>
                    </div>

                    <hr>
                    <h5 class="h6 text-primary">Ubah Password (Opsional)</h5>
                    <div class="form-text mb-2">Biarkan kosong jika Anda tidak ingin mengubah password.</div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Password Baru</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-save me-2"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
