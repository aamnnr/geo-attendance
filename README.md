# Sistem Presensi Sekolah

Sistem presensi berbasis web dan mobile untuk sekolah yang menggunakan teknologi geolokasi untuk memastikan siswa melakukan presensi di lokasi yang benar. Sistem ini dibangun dengan Laravel dan menyediakan antarmuka untuk admin dan siswa.

## ✨ Fitur Utama

### 👨‍💼 Fitur Admin

-   **Dashboard** - Ringkasan data presensi, izin, dan statistik siswa
-   **Manajemen Siswa** - Tambah, edit, hapus data siswa
-   **Manajemen Kelas** - Kelola data kelas dan jurusan
-   **Manajemen Jadwal** - Atur jadwal pelajaran per kelas
-   **Monitoring Presensi** - Lihat rekap presensi harian/bulanan
-   **Manajemen Izin** - Approve/reject permohonan izin siswa
-   **Ekspor Data** - Export laporan presensi ke Excel/PDF
-   **Pengaturan Sistem** - Konfigurasi lokasi sekolah dan radius presensi
-   **Log Aktivitas** - Monitoring aktivitas sistem

### 👨‍🎓 Fitur Siswa

-   **Presensi Online** - Check-in/check-out dengan validasi lokasi GPS
-   **Lihat Jadwal** - Akses jadwal pelajaran kelas
-   **Rekap Presensi** - Lihat riwayat presensi pribadi
-   **Pengajuan Izin** - Ajukan izin dengan upload bukti
-   **Profile Management** - Update data pribadi

### 📱 API Mobile

-   RESTful API untuk integrasi aplikasi mobile
-   Authentication dengan Laravel Sanctum
-   Endpoint untuk presensi, jadwal, dan data siswa

## 🛠️ Tech Stack

-   **Backend**: Laravel 12.0
-   **Database**: SQLite (default), MySQL/MariaDB/PostgreSQL
-   **Frontend**: Blade Templates, Bootstrap 5, Tailwind CSS
-   **JavaScript**: Vite, Axios
-   **Authentication**: Laravel Sanctum
-   **Export**: Laravel Excel, DomPDF
-   **Queue**: Laravel Queue (untuk background jobs)

## 📋 Prerequisites

-   PHP >= 8.2
-   Composer
-   Node.js & NPM
-   SQLite / MySQL / PostgreSQL

## 🚀 Instalasi

1. **Clone Repository**

    ```bash
    git clone <repository-url>
    cd geo-attendance
    ```

2. **Install Dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Environment Setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Database Configuration**
   Edit file `.env` untuk konfigurasi database:

    ```env
    DB_CONNECTION=sqlite
    # atau untuk MySQL:
    # DB_CONNECTION=mysql
    # DB_HOST=127.0.0.1
    # DB_PORT=3306
    # DB_DATABASE=geo_attendance
    # DB_USERNAME=your_username
    # DB_PASSWORD=your_password
    ```

5. **Database Migration & Seeding**

    ```bash
    php artisan migrate
    php artisan db:seed
    ```

6. **Build Assets**

    ```bash
    npm run build
    # atau untuk development:
    npm run dev
    ```

7. **Storage Link** (untuk upload file)
    ```bash
    php artisan storage:link
    ```

## ⚙️ Konfigurasi

### Lokasi Sekolah

Lokasi sekolah diatur secara dinamis melalui dashboard admin di menu **Settings**. Admin dapat mengubah koordinat GPS dan radius presensi sesuai kebutuhan sekolah.

Untuk konfigurasi awal, edit file `config/lokasi.php`:

```php
return [
    'latitude' => -7.7956,  // Koordinat Yogyakarta
    'longitude' => 110.3695,
    'radius' => 100, // Radius dalam meter
];
```

### Queue Worker (Opsional)

Untuk menjalankan background jobs:

```bash
php artisan queue:work
```

## 🏃‍♂️ Menjalankan Aplikasi

### Development Mode

```bash
composer run dev
```

Perintah ini akan menjalankan:

-   Laravel server di `http://localhost:8000`
-   Queue worker
-   Log viewer
-   Vite dev server

### Production Mode

```bash
php artisan serve
```

## 📚 API Documentation

### Authentication

Semua endpoint API (kecuali login) memerlukan Bearer token.

#### Login Admin

```http
POST /api/admin/login
Content-Type: application/json

{
    "username": "admin",
    "password": "password"
}
```

#### Login Siswa

```http
POST /api/siswa/login
Content-Type: application/json

{
    "nis": "22001",
    "password": "123456"
}
```

### Endpoint Utama

#### Admin API

-   `GET /api/admin/dashboard` - Data dashboard
-   `GET /api/admin/siswa` - List siswa
-   `POST /api/admin/siswa` - Tambah siswa
-   `GET /api/admin/kelas` - List kelas
-   `GET /api/admin/jadwal` - List jadwal
-   `GET /api/admin/rekap` - Data rekap presensi
-   `GET /api/admin/izin` - List permohonan izin
-   `POST /api/admin/izin/{id}/approve` - Approve izin
-   `POST /api/admin/izin/{id}/reject` - Reject izin

#### Siswa API

-   `GET /api/siswa/me` - Data profil siswa
-   `GET /api/siswa/kelas` - Data kelas siswa
-   `GET /api/siswa/jadwal` - Jadwal pelajaran
-   `GET /api/siswa/rekap` - Rekap presensi pribadi
-   `GET /api/siswa/izin` - List izin siswa
-   `POST /api/siswa/izin` - Ajukan izin
-   `GET /api/siswa/presensi` - Data presensi hari ini
-   `POST /api/siswa/presensi` - Lakukan presensi

## 📊 Struktur Database

### Tabel Utama

-   **admins** - Data administrator
-   **siswas** - Data siswa
-   **kelas** - Data kelas/jurusan
-   **jadwals** - Jadwal pelajaran
-   **rekaps** - Rekap presensi harian
-   **izins** - Data permohonan izin
-   **logs** - Log aktivitas sistem
-   **settings** - Pengaturan sistem

### Relasi

-   Siswa belongs to Kelas
-   Jadwal belongs to Kelas
-   Rekap belongs to Siswa
-   Izin belongs to Siswa dan Admin

## 🔧 Commands

### Artisan Commands

```bash
# Setup lengkap (install, migrate, seed, build)
composer run setup

# Jalankan development server
php artisan serve

# Jalankan queue worker
php artisan queue:work

# Jalankan tests
php artisan test

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Custom Commands

```bash
# Tandai siswa alpa otomatis
php artisan rekap:alpa
```

## 📱 Penggunaan

### Akses Web

1. Buka `http://localhost:8000`
2. Pilih login sebagai Admin atau Siswa

### Data Login Default

**Admin:**

-   Username: `admin`
-   Password: `password`

**Siswa:**

-   NIS: `22001` (Budi Santoso)
-   Password: `123456`

### Presensi Siswa

1. Login sebagai siswa
2. Akses menu "Presensi"
3. Klik tombol presensi (akan validasi lokasi GPS)
4. Sistem akan mencatat waktu check-in/check-out

## 🤝 Contributing

1. Fork repository
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📝 Testing

```bash
# Jalankan semua tests
composer run test

# Jalankan tests dengan coverage
php artisan test --coverage
```

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

## 👥 Support

Untuk pertanyaan atau dukungan, silakan buat issue di repository ini.

---

**Catatan:** Admin dapat mengatur koordinat GPS dan radius presensi melalui menu Settings di dashboard admin untuk memastikan validasi lokasi yang akurat.
