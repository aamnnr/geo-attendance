import { L, createMap, distance, dotIcon, schoolIcon } from './geo';

/**
 * Halaman presensi siswa: memantau lokasi secara live, menampilkan jarak ke
 * sekolah dan hanya mengaktifkan tombol saat siswa berada dalam radius.
 * Validasi final tetap dilakukan di server (PresensiService).
 *
 * Catatan: elemen yang bergantung pada `status` di view memakai :hidden,
 * bukan x-show — x-show bisa macet bila status berganti beberapa kali
 * dalam satu tick (mis. dua callback geolokasi gagal bersamaan).
 */
export function presensiMap({ school }) {
    let map, userMarker, accuracyCircle, watchId;

    return {
        status: 'locating', // locating | ready | error
        error: '',
        lat: null,
        lng: null,
        accuracy: null,
        distance: null,
        submitting: false,

        get inside() {
            return this.distance !== null && this.distance <= school.radius;
        },

        get distanceLabel() {
            if (this.distance === null) return '—';
            return this.distance >= 1000 ? `${(this.distance / 1000).toFixed(2)} km` : `${Math.round(this.distance)} m`;
        },

        init() {
            map = createMap(this.$refs.map, school.lat, school.lng, 17);
            L.marker([school.lat, school.lng], { icon: schoolIcon() }).addTo(map).bindTooltip(school.nama);
            L.circle([school.lat, school.lng], {
                radius: school.radius,
                color: '#f5b622',
                weight: 2,
                fillColor: '#f5b622',
                fillOpacity: 0.12,
            }).addTo(map);

            this.locate();
        },

        destroy() {
            if (watchId !== undefined) navigator.geolocation.clearWatch(watchId);
        },

        get lowAccuracy() {
            return this.accuracy !== null && this.accuracy > school.radius;
        },

        /**
         * Dua tahap supaya juga cepat di laptop (tanpa GPS):
         * 1. Lokasi jaringan (Wi-Fi/IP) — cepat, boleh cache 1 menit.
         * 2. Pantau lokasi akurasi tinggi (GPS bila ada) dan perbarui terus.
         * Error hanya ditampilkan bila belum ada lokasi sama sekali; watchPosition
         * tetap mencoba lagi setelah timeout, jadi status pulih sendiri.
         */
        locate() {
            if (!('geolocation' in navigator)) {
                this.fail('Browser Anda tidak mendukung geolokasi.');
                return;
            }

            if (!window.isSecureContext) {
                this.fail('Lokasi hanya bisa diakses lewat HTTPS atau localhost. Buka aplikasi melalui alamat HTTPS.');
                return;
            }

            this.status = 'locating';
            this.error = '';
            if (watchId !== undefined) navigator.geolocation.clearWatch(watchId);

            const onError = (err) => {
                if (this.lat === null) this.fail(messageFor(err));
            };

            navigator.geolocation.getCurrentPosition((pos) => this.update(pos.coords), (err) => {
                // Timeout tahap cepat diabaikan; tahap presisi masih berjalan.
                if (err.code !== err.TIMEOUT) onError(err);
            }, { enableHighAccuracy: false, timeout: 10000, maximumAge: 60000 });

            watchId = navigator.geolocation.watchPosition((pos) => this.update(pos.coords), onError, {
                enableHighAccuracy: true,
                timeout: 30000,
                maximumAge: 10000,
            });
        },

        update({ latitude, longitude, accuracy }) {
            const first = this.lat === null;
            Object.assign(this, { lat: latitude, lng: longitude, accuracy: Math.round(accuracy), status: 'ready', error: '' });
            this.distance = distance(school.lat, school.lng, latitude, longitude);

            if (!userMarker) {
                accuracyCircle = L.circle([latitude, longitude], { radius: accuracy, color: '#3b82f6', weight: 1, fillOpacity: 0.08 }).addTo(map);
                userMarker = L.marker([latitude, longitude], { icon: dotIcon('#3b82f6') }).addTo(map).bindTooltip('Lokasi Anda');
            } else {
                userMarker.setLatLng([latitude, longitude]);
                accuracyCircle.setLatLng([latitude, longitude]).setRadius(accuracy);
            }

            if (first) {
                map.fitBounds(L.latLngBounds([[school.lat, school.lng], [latitude, longitude]]).pad(0.35), { maxZoom: 18 });
            }
        },

        fail(message) {
            this.status = 'error';
            this.error = message;
        },

        submit() {
            if (!this.inside || this.submitting) return;
            this.submitting = true;
            this.$refs.form.submit();
        },
    };
}

function messageFor(err) {
    switch (err.code) {
        case err.PERMISSION_DENIED:
            return 'Akses lokasi ditolak. Izinkan akses lokasi di pengaturan browser, lalu coba lagi.';
        case err.POSITION_UNAVAILABLE:
            return 'Lokasi tidak tersedia. Aktifkan GPS di ponsel, atau layanan lokasi Windows/macOS dan Wi-Fi di laptop.';
        case err.TIMEOUT:
            return 'Waktu mencari lokasi habis. Pastikan layanan lokasi perangkat dan Wi-Fi aktif, lalu coba lagi.';
        default:
            return 'Gagal mendapatkan lokasi.';
    }
}
