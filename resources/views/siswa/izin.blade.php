@extends('layouts.siswa')

@section('title', 'Pengajuan Izin')
@section('page-title', 'Pengajuan Izin')

@section('content')
<div class="row g-4">
    <!-- Kolom Form Pengajuan Izin -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Formulir Pengajuan Izin</h5>
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
                
                <!-- 
                  WAJIB: tambahkan enctype="multipart/form-data" 
                  agar upload file bukti berfungsi.
                -->
                <form method="POST" action="{{ route('siswa.izin.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="keterangan" class="form-label fw-semibold">Keterangan Izin</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Contoh: Sakit demam" required>{{ old('keterangan') }}</textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="izin_dari" class="form-label fw-semibold">Dari Tanggal</label>
                            <input type="date" name="izin_dari" id="izin_dari" class="form-control" value="{{ old('izin_dari') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="izin_sampai" class="form-label fw-semibold">Sampai Tanggal</label>
                            <input type="date" name="izin_sampai" id="izin_sampai" class="form-control" value="{{ old('izin_sampai') }}" required>
                        </div>
                    </div>

                    <!-- Form Upload Bukti -->
                    <div class="mb-3">
                        <label for="bukti" class="form-label fw-semibold">Bukti (Opsional)</label>
                        <input type="file" name="bukti" id="bukti" class="form-control">
                        <div class="form-text">Upload surat dokter atau bukti pendukung (JPG, PNG, PDF). Maks 2MB.</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-send me-2"></i>Kirim Pengajuan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Riwayat Pengajuan Izin -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">Riwayat Pengajuan Anda</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Bukti</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatIzin as $izin)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($izin->izin_dari)->isoFormat('D MMM Y') }}
                                    <small class="d-block text-muted">{{ Str::limit($izin->keterangan, 30) }}</small>
                                </td>
                                <td>
                                    <!-- Tombol Lihat Bukti -->
                                    @if($izin->bukti)
                                        <a href="{{ Storage::url($izin->bukti) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- Status Badge -->
                                    @if($izin->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($izin->status == 'disetujui')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($izin->status == 'ditolak')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- Tombol Batalkan -->
                                    @if($izin->status == 'pending')
                                        <form action="{{ route('siswa.izin.destroy', ['izin' => $izin->izin_id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan izin ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted p-4">
                                    Belum ada riwayat pengajuan izin.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Links -->
                <div class="mt-3">
                    {{ $riwayatIzin->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

