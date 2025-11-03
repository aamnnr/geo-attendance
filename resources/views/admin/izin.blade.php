@extends('layouts.app')

@section('title', 'Manajemen Izin')
@section('page-title', 'Manajemen Izin Siswa')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Tanggal Izin</th>
                        <th>Keterangan</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- 
                      --- INI PERBAIKANNYA ---
                      Mengganti $riwayatIzin menjadi $izins (sesuai kiriman controller)
                      Menggunakan $item sebagai variabel loop
                    -->
                    @forelse($izins as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->siswa->siswa_nama ?? 'N/A' }}</strong>
                            <small class="d-block text-muted">NIS: {{ $item->siswa->siswa_nis ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $item->siswa->kelas->kelas_nama ?? 'N/A' }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($item->izin_dari)->isoFormat('D MMM Y') }}
                            -
                            {{ \Carbon\Carbon::parse($item->izin_sampai)->isoFormat('D MMM Y') }}
                        </td>
                        <td>{{ $item->keterangan }}</td>
                        <td>
                            @if($item->bukti)
                                <a href="{{ Storage::url($item->bukti) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($item->status == 'disetujui')
                                <span class="badge bg-success">Disetujui</span>
                            @elseif($item->status == 'ditolak')
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                            
                            @if($item->status != 'pending' && $item->admin)
                                <small class="d-block text-muted mt-1" style="font-size: 0.75rem;">
                                    Oleh: {{ $item->admin->admin_nama }}
                                </small>
                            @endif
                        </td>
                        <td>
                            @if($item->status == 'pending')
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- 
                                  PERBAIKAN NAMA RUTE: 
                                  Menggunakan array ['izin' => $item->izin_id] agar cocok
                                  dengan parameter rute {izin}.
                                -->
                                <form action="{{ route('admin.izin.approve', ['izin' => $item->izin_id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Setujui izin ini?')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.izin.reject', ['izin' => $item->izin_id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Tolak izin ini?')">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            </div>
                            @else
                                <span class="text-muted">Diproses</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted p-4"> <!-- Colspan diubah jadi 7 -->
                            Belum ada pengajuan izin.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $izins->links() }}
        </div>
    </div>
</div>
@endsection

