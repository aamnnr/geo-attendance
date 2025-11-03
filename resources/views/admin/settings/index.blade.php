@extends('layouts.app')

@section('title', 'Pengaturan Presensi')
@section('page-title', 'Pengaturan Presensi')

@push('styles')
    <!-- CSS Leaflet.js tanpa integrity hash -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { 
            height: 400px; 
            border-radius: 8px;
            z-index: 1;
            border: 1px solid #dee2e6;
        }
        .radius-info {
            background-color: #e7f3ff;
            border-left: 4px solid #0d6efd;
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .map-container {
            position: relative;
        }
        .loading-map {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            background: rgba(255,255,255,0.9);
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .leaflet-container {
            font-family: inherit !important;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Pengaturan Lokasi Presensi</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Atur titik koordinat pusat dan radius toleransi untuk presensi siswa. Anda bisa mengklik peta atau menyeret penanda untuk mengatur koordinat.</p>

                <!-- Tampilkan pesan sukses -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

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

                <div class="radius-info">
                    <small><i class="bi bi-info-circle me-1"></i> Lingkaran biru pada peta menunjukkan radius toleransi presensi saat ini.</small>
                </div>

                <!-- Container Peta dengan Loading -->
                <div class="map-container mb-3">
                    <div id="map"></div>
                    <div id="mapLoading" class="loading-map">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Memuat peta...
                    </div>
                </div>

                <form action="{{ route('admin.settings.update') }}" method="POST" id="locationForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="school_lat" class="form-label fw-semibold">Latitude</label>
                                <input type="text" class="form-control" id="school_lat" name="school_lat" 
                                       value="{{ old('school_lat', $settings['school_lat']->value ?? '-6.2088') }}" 
                                       placeholder="Contoh: -6.123456" required>
                                <div class="form-text">Format: angka desimal antara -90 dan 90</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="school_lng" class="form-label fw-semibold">Longitude</label>
                                <input type="text" class="form-control" id="school_lng" name="school_lng" 
                                       value="{{ old('school_lng', $settings['school_lng']->value ?? '106.8456') }}" 
                                       placeholder="Contoh: 106.123456" required>
                                <div class="form-text">Format: angka desimal antara -180 dan 180</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="school_radius" class="form-label fw-semibold">Radius Toleransi (dalam meter)</label>
                        <input type="number" class="form-control" id="school_radius" name="school_radius" 
                               value="{{ old('school_radius', $settings['school_radius']->value ?? '100') }}" 
                               placeholder="Contoh: 100" min="10" required>
                        <div class="form-text">Jarak toleransi minimum adalah 10 meter.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-save me-2"></i>Simpan Pengaturan
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Instruksi</h5>
                <p class="text-muted small">
                    <strong>Peta:</strong> Klik lokasi di peta atau seret penanda <i class="bi bi-geo-alt-fill text-danger"></i> untuk memilih lokasi. Form Latitude dan Longitude akan terisi otomatis.
                </p>
                <p class="text-muted small">
                    <strong>Input Manual:</strong> Anda juga bisa mengetik Latitude/Longitude secara manual, dan peta akan otomatis berpindah ke lokasi tersebut.
                </p>
                <p class="text-muted small">
                    <strong>Radius:</strong> Lingkaran biru menunjukkan area presensi yang valid. Siswa harus berada dalam radius ini untuk dapat melakukan presensi.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- JS Leaflet.js tanpa integrity hash -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Memulai inisialisasi peta...');
            
            // Ambil elemen input
            const latInput = document.getElementById('school_lat');
            const lngInput = document.getElementById('school_lng');
            const radiusInput = document.getElementById('school_radius');
            const mapLoading = document.getElementById('mapLoading');

            // Ambil nilai awal dari input dengan fallback
            let currentLat = parseFloat(latInput.value) || -6.2088;
            let currentLng = parseFloat(lngInput.value) || 106.8456;
            let currentRadius = parseInt(radiusInput.value) || 100;

            console.log('Koordinat awal:', currentLat, currentLng);
            console.log('Radius awal:', currentRadius);

            // Inisialisasi peta
            try {
                const map = L.map('map').setView([currentLat, currentLng], 16);
                console.log('Peta berhasil diinisialisasi');

                // Tambahkan layer tile (tampilan peta)
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Tambahkan marker (penanda) yang bisa diseret
                let marker = L.marker([currentLat, currentLng], {
                    draggable: true,
                    title: 'Lokasi Presensi'
                }).addTo(map);

                // Tambahkan circle untuk menunjukkan radius
                let circle = L.circle([currentLat, currentLng], {
                    color: 'blue',
                    fillColor: '#0d6efd',
                    fillOpacity: 0.2,
                    radius: currentRadius
                }).addTo(map);

                // Popup info
                marker.bindPopup('<b>Lokasi Presensi</b><br>Lat: ' + currentLat + '<br>Lng: ' + currentLng).openPopup();

                // Sembunyikan loading setelah peta siap
                map.whenReady(function() {
                    console.log('Peta siap');
                    setTimeout(() => {
                        mapLoading.style.display = 'none';
                        // Perbaiki ukuran peta
                        map.invalidateSize();
                    }, 500);
                });

                // Fungsi untuk memperbarui semua elemen peta dan form
                function updateAll(latlng, radius = currentRadius) {
                    // Update marker position
                    marker.setLatLng(latlng);
                    
                    // Update circle position and radius
                    circle.setLatLng(latlng);
                    circle.setRadius(radius);
                    
                    // Update form inputs
                    latInput.value = latlng.lat.toFixed(7);
                    lngInput.value = latlng.lng.toFixed(7);
                    
                    // Update popup
                    marker.bindPopup('<b>Lokasi Presensi</b><br>Lat: ' + latlng.lat.toFixed(7) + '<br>Lng: ' + latlng.lng.toFixed(7)).openPopup();
                    
                    // Update current values
                    currentRadius = radius;
                    
                    console.log('Koordinat diperbarui:', latlng.lat.toFixed(7), latlng.lng.toFixed(7));
                }

                // Event listener saat marker selesai digeser
                marker.on('dragend', function(e) {
                    updateAll(e.target.getLatLng());
                });

                // Event listener saat peta diklik
                map.on('click', function(e) {
                    updateAll(e.latlng);
                });

                // Fungsi untuk memperbarui peta jika input diubah manual
                function updateMapFromInputs() {
                    const newLat = parseFloat(latInput.value);
                    const newLng = parseFloat(lngInput.value);
                    const newRadius = parseInt(radiusInput.value);

                    if (!isNaN(newLat) && !isNaN(newLng) && !isNaN(newRadius)) {
                        // Validasi range koordinat
                        if (newLat >= -90 && newLat <= 90 && newLng >= -180 && newLng <= 180) {
                            const newLatLng = L.latLng(newLat, newLng);
                            updateAll(newLatLng, newRadius);
                            map.panTo(newLatLng);
                        } else {
                            alert('Koordinat tidak valid. Latitude harus antara -90 dan 90, Longitude antara -180 dan 180.');
                        }
                    }
                }

                // Tambahkan event listener ke input form
                latInput.addEventListener('change', updateMapFromInputs);
                lngInput.addEventListener('change', updateMapFromInputs);
                radiusInput.addEventListener('change', updateMapFromInputs);

                // Fungsi untuk memperbaiki ukuran peta
                function fixMapSize() {
                    setTimeout(() => {
                        map.invalidateSize();
                        console.log('Ukuran peta diperbaiki');
                    }, 300);
                }

                // Panggil fungsi perbaikan ukuran peta setelah delay
                setTimeout(fixMapSize, 1000);

                // Tambahkan event listener untuk window resize
                window.addEventListener('resize', fixMapSize);

            } catch (error) {
                console.error('Error inisialisasi peta:', error);
                mapLoading.innerHTML = 'Error memuat peta: ' + error.message;
            }

            // Validasi form sebelum submit
            document.getElementById('locationForm').addEventListener('submit', function(e) {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                const radius = parseInt(radiusInput.value);
                
                if (isNaN(lat) || isNaN(lng) || isNaN(radius)) {
                    e.preventDefault();
                    alert('Pastikan semua field diisi dengan nilai yang valid.');
                    return;
                }
                
                if (lat < -90 || lat > 90) {
                    e.preventDefault();
                    alert('Latitude harus antara -90 dan 90 derajat.');
                    return;
                }
                
                if (lng < -180 || lng > 180) {
                    e.preventDefault();
                    alert('Longitude harus antara -180 dan 180 derajat.');
                    return;
                }
                
                if (radius < 10) {
                    e.preventDefault();
                    alert('Radius harus minimal 10 meter.');
                    return;
                }
            });
        });
    </script>
@endpush