<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Presensi Sekolah</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .landing-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="landing-gradient min-vh-100">
    <div class="min-vh-100 d-flex align-items-center justify-content-center p-3">
        <div class="card shadow-lg border-0 rounded-3" style="max-width: 450px; width: 100%;">
            <div class="card-body p-4">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold text-primary mb-2">Sistem Presensi</h1>
                    <p class="text-muted">Silakan pilih jenis login</p>
                </div>

                <div class="d-grid gap-3">
                    <!-- 👨‍💼 Login Admin -->
                    <a href="{{ route('admin.login') }}" 
                       class="btn btn-primary btn-lg py-3 fw-semibold card-hover">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-person-gear me-2"></i>
                            Login sebagai Admin
                        </div>
                    </a>

                    <!-- 👨‍🎓 Login Siswa -->
                    <a href="{{ route('siswa.login') }}" 
                       class="btn btn-success btn-lg py-3 fw-semibold card-hover">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-person-circle me-2"></i>
                            Login sebagai Siswa
                        </div>
                    </a>

                    <!-- 📱 Info Aplikasi -->
                    <div class="card border-light bg-light mt-3">
                        <div class="card-body text-center py-3">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="bi bi-phone text-secondary me-2"></i>
                                <span class="fw-semibold text-dark">Akses Melalui Aplikasi</span>
                            </div>
                            <p class="text-muted small mb-2">
                                Siswa juga dapat menggunakan aplikasi mobile untuk presensi dan melihat jadwal.
                            </p>
                            <div class="text-muted small">
                                Download aplikasi di Play Store / App Store
                            </div>
                        </div>
                    </div>

                    <!-- 📱 QR Code untuk Download App (Opsional) -->
                    <div class="text-center mt-3">
                        <div class="card border-light d-inline-block">
                            <div class="card-body p-3">
                                <div class="bg-light rounded d-flex align-items-center justify-content-center mb-2 mx-auto" 
                                     style="width: 120px; height: 120px;">
                                    <span class="text-muted small">QR Code</span>
                                </div>
                                <p class="text-muted small mb-0">Scan untuk download app</p>
                            </div>
                        </div>
                    </div>
                </div> <!-- end d-grid -->
            </div>
        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
