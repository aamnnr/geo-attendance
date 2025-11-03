@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah Rekap Absensi</h3>
    <form action="{{ route('admin.rekap.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Siswa</label>
            <select name="siswa_id" class="form-control" required>
                <option value="">-- Pilih Siswa --</option>
                @foreach ($siswas as $s)
                    <option value="{{ $s->siswa_id }}">
                        {{ $s->siswa_nama }} ({{ $s->kelas->kelas_nama ?? '-' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>

        <div class="row">
            <div class="col">
                <label>Jam Masuk</label>
                <input type="time" name="jam_masuk" class="form-control">
            </div>
            <div class="col">
                <label>Jam Keluar</label>
                <input type="time" name="jam_keluar" class="form-control">
            </div>
        </div>

        <div class="mb-3 mt-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="Hadir">Hadir</option>
                <option value="Izin">Izin</option>
                <option value="Sakit">Sakit</option>
                <option value="Alpa">Alpa</option>
            </select>
        </div>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('admin.rekap.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
