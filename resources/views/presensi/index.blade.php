@extends('layouts.app')

@section('title', 'Presensi Real-time')

@section('page-title', 'Monitoring Presensi Real-time')

@section('content')
<div class="card shadow border-0">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <h4 id="total-hadir">0</h4>
                        <p>Hadir Hari Ini</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-warning text-white">
                    <div class="card-body">
                        <h4 id="total-terlambat">0</h4>
                        <p>Terlambat</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h4 id="total-masuk">0</h4>
                        <p>Sudah Masuk</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-info text-white">
                    <div class="card-body">
                        <h4 id="total-pulang">0</h4>
                        <p>Sudah Pulang</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover" id="presensi-table">
                <thead class="table-dark">
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan di-load via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@section('scripts')
<script>
// JavaScript untuk real-time update (contoh sederhana)
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk update data presensi
    function updatePresensiData() {
        // Di sini bisa diisi dengan AJAX request ke API
        console.log('Updating presensi data...');
    }

    // Update setiap 30 detik
    setInterval(updatePresensiData, 30000);
    
    // Initial load
    updatePresensiData();
});
</script>
@endsection
@endsection