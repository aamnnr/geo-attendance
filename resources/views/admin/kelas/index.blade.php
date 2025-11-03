@extends('layouts.app')

@section('title', 'Data Kelas')

@section('page-title', 'Data Kelas')

@section('page-actions')
    <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Tambah Kelas
    </a>
@endsection

@section('content')

<div class="card shadow border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelas as $k)
                    <tr>
                        <td>{{ $k->kelas_id }}</td>
                        <td>{{ $k->kelas_nama }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.kelas.edit', $k->kelas_id) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.kelas.destroy', $k->kelas_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus kelas ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-4">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <p class="text-muted mt-2">Belum ada data kelas</p>
                            <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Kelas Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection