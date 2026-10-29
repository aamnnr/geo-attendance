import { L, createMap, schoolIcon } from './geo';

/**
 * Halaman Pengaturan admin: pilih titik sekolah dengan klik/geser marker
 * dan atur radius presensi dengan slider.
 */
export function locationPicker({ lat, lng, radius }) {
    let map, marker, circle;

    return {
        lat: Number(lat),
        lng: Number(lng),
        radius: Number(radius),
        locating: false,

        init() {
            map = createMap(this.$refs.map, this.lat, this.lng, 17);
            marker = L.marker([this.lat, this.lng], { icon: schoolIcon(), draggable: true }).addTo(map);
            circle = L.circle([this.lat, this.lng], {
                radius: this.radius,
                color: '#f5b622',
                weight: 2,
                fillColor: '#f5b622',
                fillOpacity: 0.15,
            }).addTo(map);

            marker.on('drag', (e) => this.setPoint(e.latlng.lat, e.latlng.lng, false));
            map.on('click', (e) => this.setPoint(e.latlng.lat, e.latlng.lng));

            this.$watch('radius', (r) => circle.setRadius(Number(r) || 0));
            this.$watch('lat', () => this.syncFromInputs());
            this.$watch('lng', () => this.syncFromInputs());
        },

        setPoint(lat, lng, moveMarker = true) {
            this.lat = Number(lat.toFixed(7));
            this.lng = Number(lng.toFixed(7));
            if (moveMarker) marker.setLatLng([this.lat, this.lng]);
            circle.setLatLng([this.lat, this.lng]);
        },

        syncFromInputs() {
            if (Number.isFinite(+this.lat) && Number.isFinite(+this.lng)) {
                marker.setLatLng([this.lat, this.lng]);
                circle.setLatLng([this.lat, this.lng]);
            }
        },

        useMyLocation() {
            if (!('geolocation' in navigator)) return;
            this.locating = true;
            navigator.geolocation.getCurrentPosition(
                ({ coords }) => {
                    this.setPoint(coords.latitude, coords.longitude);
                    map.setView([this.lat, this.lng], 18);
                    this.locating = false;
                },
                () => {
                    this.locating = false;
                    Alpine.store('toast').push('Tidak bisa mengambil lokasi perangkat.', 'error');
                },
                { enableHighAccuracy: true, timeout: 10000 },
            );
        },

        recenter() {
            map.setView([this.lat, this.lng], 17);
        },
    };
}
