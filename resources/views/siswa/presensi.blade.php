@extends('layouts.siswa')

@section('title', 'Presensi')
@section('page-title', 'Presensi Kehadiran')

@section('content')
<div classT="row">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 text-center">
                
                <h4 class="card-title">Presensi Berbasis Lokasi</h4>
                <p class="text-muted">Pastikan Anda berada di area sekolah dan izinkan browser mengakses lokasi Anda.</p>
                
                <!-- 
                  --- INI PERBAIKANNYA ---
                  Menambahkan div untuk menampilkan pesan error atau sukses 
                -->
                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif
                <!-- Selesai Perbaikan -->

                <button id="btnPresensi" class="btn btn-primary btn-lg w-100 py-3 mt-3">
                    <span id="btnText">
                        <i class="bi bi-geo-alt me-2"></i>Ambil Lokasi & Presensi Sekarang
                    </span>
                    <span id="btnLoading" class="d-none">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Mencari lokasi...
                    </span>
                </button>

                <!-- Form tersembunyi untuk mengirim data lokasi -->
                <form id="formPresensi" action="{{ route('siswa.presensi.store') }}" method="POST" class="d-none">
                    @csrf
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                </form>

                <div class="alert alert-info mt-3 d-none" id="lokasiInfo">
                    Lokasi Anda: <span id="infoCoords"></span><br>
                    Akurasi: <span id="infoAkurasi"></span> meter
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnPresensi = document.getElementById('btnPresensi');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');
        
        const formPresensi = document.getElementById('formPresensi');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        const lokasiInfo = document.getElementById('lokasiInfo');
        const infoCoords = document.getElementById('infoCoords');
        const infoAkurasi = document.getElementById('infoAkurasi');

        btnPresensi.addEventListener('click', () => {
            if (!navigator.geolocation) {
                alert('Geolocation tidak didukung oleh browser Anda.');
                return;
            }

            // Tampilkan loading
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            btnPresensi.disabled = true;

            // Opsi untuk akurasi tinggi
            const options = {
                enableHighAccuracy: true,
                timeout: 10000, // 10 detik timeout
                maximumAge: 0 // Jangan gunakan cache lokasi
            };

            navigator.geolocation.getCurrentPosition(pos => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                const accuracy = pos.coords.accuracy;

                // Tampilkan info lokasi (opsional)
                infoCoords.textContent = `${lat}, ${lng}`;
                infoAkurasi.textContent = Math.round(accuracy);
                lokasiInfo.classList.remove('d-none');
                
                // Masukkan data ke form tersembunyi
                latInput.value = lat;
                lngInput.value = lng;

                // Kirim form
                formPresensi.submit();

            }, err => {
                // Sembunyikan loading
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
                btnPresensi.disabled = false;

                // Tampilkan error
                let errorMsg = "Gagal mendapatkan lokasi. ";
                switch(err.code) {
                    case err.PERMISSION_DENIED:
                        errorMsg += "Anda menolak izin akses lokasi.";
                        break;
                    case err.POSITION_UNAVAILABLE:
                        errorMsg += "Informasi lokasi tidak tersedia.";
                        break;
                    case err.TIMEOUT:
                        errorMsg += "Waktu permintaan lokasi habis (timeout). Coba lagi.";
                        break;
                    default:
                        errorMsg += "Terjadi kesalahan tidak diketahui.";
                        break;
                }
                // Tampilkan error di div info agar lebih jelas
                infoCoords.textContent = errorMsg;
                infoAkurasi.textContent = '';
                lokasiInfo.classList.remove('d-none');
                lokasiInfo.classList.remove('alert-info');
                lokasiInfo.classList.add('alert-danger');

            }, options);
        });
    });
</script>
@endpush

