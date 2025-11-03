@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')
@section('page-title', 'Dashboard') <!-- Tambahkan ini agar judul halaman di layout terisi -->

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- PERBAIKAN 1: Mengganti 'nama' menjadi 'siswa_nama' -->
        <h4 class="card-title mb-1">Selamat Datang, {{ session('siswa.siswa_nama') ?? 'Siswa' }} 👋</h4>
        
        <!-- PERBAIKAN 2: Menambahkan Hari, Tanggal, dan Jam Live -->
        <div class="mb-3">
            <p class="fs-5 text-primary fw-semibold mb-0" id="tanggal-hari">Memuat tanggal...</p>
            <p class="fs-2 fw-bold text-dark" id="jam-live">Memuat jam...</p>
        </div>
        
        <p class="card-text text-muted">
            Di sini kamu bisa melakukan presensi, melihat rekap kehadiran, dan mengajukan izin.
        </p>
        
        <div class="list-group mt-4">
            <a href="{{ route('siswa.presensi') }}" class="list-group-item list-group-item-action py-3">
                <i class="bi bi-geo-alt-fill me-2"></i>Presensi Berdasarkan Lokasi
            </a>
            <a href="{{ route('siswa.rekap') }}" class="list-group-item list-group-item-action py-3">
                <i class="bi bi-clipboard-check me-2"></i>Rekap Presensi
            </a>
            <a href="{{ route('siswa.izin') }}" class="list-group-item list-group-item-action py-3">
                <i class="bi bi-envelope-paper me-2"></i>Ajukan Izin
            </a>
            <a href="{{ route('siswa.profile') }}" class="list-group-item list-group-item-action py-3">
                <i class="bi bi-person-lines-fill me-2"></i>Info Akun / Data Diri
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- PERBAIKAN 3: JavaScript untuk Jam dan Tanggal Live -->
<script>
    function updateWaktu() {
        const now = new Date();

        // 1. Format Hari dan Tanggal (Contoh: Sabtu, 1 November 2025)
        const optionsTanggal = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        const tanggalStr = now.toLocaleDateString('id-ID', optionsTanggal);
        
        // 2. Format Jam (Contoh: 22:30:05)
        const jam = String(now.getHours()).padStart(2, '0');
        const menit = String(now.getMinutes()).padStart(2, '0');
        const detik = String(now.getSeconds()).padStart(2, '0');
        const jamStr = `${jam}:${menit}:${detik}`;

        // 3. Update elemen di halaman
        document.getElementById('tanggal-hari').innerHTML = tanggalStr;
        document.getElementById('jam-live').innerHTML = jamStr;
    }

    // Panggil fungsi saat halaman dimuat
    updateWaktu();

    // Update jam setiap 1 detik (1000 milidetik)
    setInterval(updateWaktu, 1000);
</script>
@endpush
