@extends('layouts.app')

@section('title', 'Data Rekap Presensi')
@section('page-title', 'Rekap Presensi')

@section('page-actions')
    <a href="{{ route('admin.rekap.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Tambah Rekap
    </a>
@endsection

@section('content')

<!-- ==== FORM FILTER ==== -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-light py-3">
        <h5 class="mb-0">
            <i class="bi bi-funnel me-2"></i>Filter Data Rekap
        </h5>
    </div>
    <!-- 
      Form ini menggunakan method GET.
      Tombol Filter akan mengirim ke 'admin.rekap.index'
      Tombol Ekspor akan mengirim ke rute ekspor menggunakan 'formaction'
    -->
    <form action="{{ route('admin.rekap.index') }}" method="GET">
        <div class="card-body">
            <div class="row g-3">
                <!-- Filter Kelas -->
                <div class="col-md-4">
                    <label for="kelas_id" class="form-label">Kelas</label>
                    <select name="kelas_id" id="kelas_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas_list as $kelas)
                            <option value="{{ $kelas->kelas_id }}" 
                                    {{ request('kelas_id') == $kelas->kelas_id ? 'selected' : '' }}>
                                {{ $kelas->kelas_nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Filter Tanggal Mulai -->
                <div class="col-md-4">
                    <label for="tanggal_mulai" class="form-label">Dari Tanggal (Minggu/Bulan)</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" 
                           class="form-control" value="{{ request('tanggal_mulai') }}">
                </div>
                <!-- Filter Tanggal Selesai -->
                <div class="col-md-4">
                    <label for="tanggal_sampai" class="form-label">Sampai Tanggal (Minggu/Bulan)</label>
                    <input type="date" name="tanggal_sampai" id="tanggal_sampai" 
                           class="form-control" value="{{ request('tanggal_sampai') }}">
                </div>
            </div>
        </div>
        <div class="card-footer bg-light text-end">
            <!-- Tombol Aksi -->
            <a href="{{ route('admin.rekap.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-clockwise me-1"></i>Reset
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search me-1"></i>Terapkan Filter
            </button>
            <button type="submit" formaction="{{ route('admin.rekap.export.excel') }}" 
                    class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>Ekspor Excel
            </button>
            <button type="submit" formaction="{{ route('admin.rekap.export.pdf') }}" 
                    class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>Ekspor PDF
            </button>
        </div>
    </form>
</div>
<!-- ==== AKHIR FORM FILTER ==== -->


<div class="card shadow border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekaps as $rekap)
                    <tr>
                        <!-- Format tanggal agar lebih rapi -->
                        <td>{{ \Carbon\Carbon::parse($rekap->tanggal)->isoFormat('D MMM Y') }}</td>
                        <td>{{ $rekap->siswa->siswa_nama ?? 'Siswa Dihapus' }}</td>
                        <td>{{ $rekap->siswa->kelas->kelas_nama ?? 'Kelas Dihapus' }}</td>
                        <td>{{ $rekap->jam_masuk ? \Carbon\Carbon::parse($rekap->jam_masuk)->format('H:i') : '-' }}</td>
                        <td>{{ $rekap->jam_keluar ? \Carbon\Carbon::parse($rekap->jam_keluar)->format('H:i') : '-' }}</td>
                        <td>
                            @if($rekap->status == 'Hadir')
                                <span class="badge bg-success">Hadir</span>
                            @elseif($rekap->status == 'Izin')
                                <span class="badge bg-warning text-dark">Izin</span>
                            @elseif($rekap->status == 'Alpha')
                                <span class="badge bg-danger">Alpha</span>
                            @elseif($rekap->status == 'Terlambat')
                                <span class="badge bg-info">Terlambat</span>
                            @else
                                <span class="badge bg-secondary">{{ $rekap->status }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.rekap.edit', $rekap->rekap_id) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.rekap.destroy', $rekap->rekap_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin hapus rekap ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-clipboard-data display-4 text-muted"></i>
                            <p class="text-muted mt-2">Tidak ada data rekap yang cocok dengan filter Anda.</p>
                            <a href="{{ route('admin.rekap.index') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset Filter
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tampilkan Pagination Links -->
        <div class="mt-3">
            {{ $rekaps->links() }}
        </div>
    </div>
</div>
@endsection
