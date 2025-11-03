@extends('layouts.app')

@section('title', 'Data Siswa')

@section('page-title', 'Data Siswa')

@section('page-actions')
    <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Tambah Siswa
    </a>
@endsection

@section('content')

<div class="card shadow border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswas as $index => $siswa)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $siswa->siswa_nis }}</td>
                        <td>{{ $siswa->siswa_nama }}</td>
                        <td>{{ $siswa->kelas->kelas_nama ?? '-' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.siswa.edit', $siswa->siswa_id) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.siswa.destroy', $siswa->siswa_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin hapus siswa ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($siswas->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-people display-4 text-muted"></i>
            <h5 class="mt-3 text-muted">Belum ada data siswa</h5>
            <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle me-1"></i>Tambah Siswa Pertama
            </a>
        </div>
        @endif
    </div>
</div>
@endsection