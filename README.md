# GeoPresensi

Sistem presensi siswa berbasis lokasi (geofencing) dengan Laravel 12. Siswa melakukan presensi masuk/pulang dari ponsel ketika berada dalam radius sekolah; admin mengelola siswa, kelas, jadwal, izin, hari libur, dan mengekspor rekap ke Excel/PDF.

## Fitur

**Portal siswa**
- Presensi masuk & pulang dengan peta live (Leaflet), jarak ke sekolah, dan validasi radius di server
- Status otomatis: *Hadir* / *Terlambat* (berdasarkan jam pelajaran pertama + toleransi)
- Riwayat presensi per bulan, jadwal pelajaran, pengajuan izin dengan lampiran (bisa dibatalkan selama menunggu)
- Profil: ubah email, HP, alamat, dan password

**Panel admin**
- Dashboard: statistik hari ini, grafik tren 7 hari, izin menunggu, aktivitas terbaru
- CRUD siswa (cari & filter), kelas, jadwal (cek bentrok jam), rekap (koreksi manual)
- Persetujuan izin (setujui / tolak dengan catatan) — izin disetujui otomatis membuat rekap *Izin/Sakit*
- Hari libur (presensi & tanda Alpa otomatis dinonaktifkan)
- Pengaturan lokasi sekolah lewat peta, radius, toleransi terlambat, batas presensi masuk
- Ekspor rekap ke Excel & PDF sesuai filter, log aktivitas

**Lainnya**
- Penandaan *Alpa* otomatis setiap hari pukul 23:59 (`php artisan rekap:alpa`)
- REST API (Sanctum) untuk aplikasi mobile

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Untuk development: `composer run dev` (server, queue, log, dan Vite sekaligus).
Agar tanda Alpa otomatis berjalan: `php artisan schedule:work`.

> Skema database telah dirapikan. Jika sebelumnya sudah punya database lama, jalankan `php artisan migrate:fresh --seed`.

### Akun demo

| Peran | Login | Password |
|-------|-------|----------|
| Admin | `admin` | `password` |
| Siswa | NIS `22001` (atau `22002`, `22003`) | `123456` |

Lokasi sekolah default diambil dari `.env` (`SCHOOL_LAT`, `SCHOOL_LNG`, `SCHOOL_RADIUS`) dan dapat diubah di menu **Pengaturan**.

## Arsitektur

```
app/
├── Enums/            StatusPresensi, StatusIzin, JenisIzin, Hari (label & warna badge)
├── Services/         PresensiService, IzinService, AlpaService, StatistikService
├── Http/
│   ├── Controllers/  Admin/*, Siswa/*, Api/Admin/*, Api/Siswa/*  (tipis, memanggil service)
│   ├── Requests/     Validasi (dipakai bersama oleh web & API)
│   └── Resources/    Format JSON API
├── Models/           Relasi, scope (filter, search), accessor
├── Casts/DateOnly    Menyimpan kolom DATE sebagai Y-m-d (konsisten di MySQL & SQLite)
└── Support/Geo       Rumus Haversine
resources/views/
├── components/       Layout, ikon, badge, modal, form, tabel, toast, dialog konfirmasi
├── admin/ · siswa/   Halaman
resources/js/components/  crudForm (modal tambah/ubah), presensiMap, locationPicker
```

- Autentikasi memakai guard Laravel `admin` dan `siswa` (bukan session manual), dengan pembatasan 5 percobaan login per menit.
- Aturan presensi hanya ada di `PresensiService`, sehingga web dan API selalu konsisten.
- Query statistik memakai `GROUP BY` (bukan satu query per status/hari).

## API

Semua endpoint berada di `/api`. Login mengembalikan Bearer token dengan ability `admin` atau `siswa`.

| Siswa | |
|---|---|
| `POST /siswa/login` | `{ nis, password }` → token |
| `GET /siswa/me` · `PUT /siswa/profil` · `POST /siswa/logout` | |
| `GET /siswa/presensi` | kondisi hari ini (`aksi`: masuk/pulang/selesai/…) |
| `POST /siswa/presensi` | `{ latitude, longitude }` → masuk atau pulang otomatis |
| `GET /siswa/rekap` · `GET /siswa/jadwal` | |
| `GET/POST /siswa/izin` · `DELETE /siswa/izin/{id}` | |

| Admin | |
|---|---|
| `POST /admin/login` | `{ username, password }` → token |
| `GET /admin/dashboard` | ringkasan & tren 7 hari |
| `apiResource`: `/admin/siswa`, `/admin/kelas`, `/admin/jadwal`, `/admin/rekap` | |
| `GET /admin/izin` · `POST /admin/izin/{id}/approve` · `POST /admin/izin/{id}/reject` | |
| `GET /admin/log` | |

## Testing

```bash
php artisan test
```

Mencakup aturan presensi (radius, terlambat, pulang, libur, izin, batas jam), alur izin, autentikasi, API, dan render seluruh halaman.
