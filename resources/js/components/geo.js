import L from 'leaflet';

/** Jarak Haversine dalam meter (sama dengan App\Support\Geo di backend). */
export function distance(lat1, lng1, lat2, lng2) {
    const R = 6371000;
    const rad = (d) => (d * Math.PI) / 180;
    const dLat = rad(lat2 - lat1);
    const dLng = rad(lng2 - lng1);
    const a = Math.sin(dLat / 2) ** 2 + Math.cos(rad(lat1)) * Math.cos(rad(lat2)) * Math.sin(dLng / 2) ** 2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

/** Peta Leaflet dengan tile OpenStreetMap. */
export function createMap(el, lat, lng, zoom = 17) {
    const map = L.map(el, { zoomControl: false, attributionControl: true }).setView([lat, lng], zoom);
    L.control.zoom({ position: 'bottomright' }).addTo(map);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap',
    }).addTo(map);
    return map;
}

/** Marker bulat (tanpa file gambar) agar tidak bergantung pada aset default Leaflet. */
export function dotIcon(color = '#f5b622', size = 18) {
    return L.divIcon({
        className: '',
        iconSize: [size, size],
        iconAnchor: [size / 2, size / 2],
        html: `<span style="display:block;width:${size}px;height:${size}px;border-radius:9999px;background:${color};border:3px solid #fff;box-shadow:0 0 0 2px ${color}55,0 4px 10px rgba(0,0,0,.25)"></span>`,
    });
}

export function schoolIcon() {
    return L.divIcon({
        className: '',
        iconSize: [36, 36],
        iconAnchor: [18, 34],
        html: `<span style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:12px 12px 12px 2px;transform:rotate(-45deg);background:#16161a;box-shadow:0 6px 14px rgba(0,0,0,.3)"><svg style="transform:rotate(45deg)" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f5b622" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c3 2 9 2 12 0v-5"/></svg></span>`,
    });
}

export { L };
