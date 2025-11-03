<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Siswa')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        /* Navbar */
        .navbar {
            z-index: 1050; /* di atas sidebar */
        }

        /* Sidebar Desktop */
        .sidebar.d-lg-block {
            position: fixed;
            top: 56px; /* tinggi navbar */
            left: 0;
            width: 240px;
            height: calc(100vh - 56px);
            background-color: #fff;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
            padding-top: 1rem;
            z-index: 1040;
        }

        .sidebar a {
            color: #333;
            font-weight: 500;
        }

        .sidebar a.active {
            background-color: #0d6efd;
            color: white !important;
            border-radius: 8px;
        }

        /* Sidebar Mobile Offcanvas */
        .offcanvas-start {
            width: 240px;
        }

        /* Main content */
        .main-content {
            margin-top: 56px; /* tinggi navbar */
        }
        @media (min-width: 992px) {
            .main-content {
                margin-left: 240px; /* untuk sidebar desktop */
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container-fluid">
            <!-- Tombol sidebar mobile -->
            <button class="btn btn-light d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                <i class="bi bi-list text-primary fs-4"></i>
            </button>

            <a class="navbar-brand fw-bold" href="{{ route('siswa.dashboard') }}">
                <i class="bi bi-person-check me-2"></i>Portal Siswa
            </a>

            <!-- Toggle dropdown user -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i> {{ session('siswa.siswa_nama') ?? 'Siswa' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('siswa.logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar Desktop -->
    <div class="sidebar d-none d-lg-block">
        <ul class="nav flex-column px-3">
            <li class="nav-item">
                <a href="{{ route('siswa.dashboard') }}" 
                   class="nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                   <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('siswa.rekap') }}" 
                   class="nav-link {{ request()->routeIs('siswa.rekap') ? 'active' : '' }}">
                   <i class="bi bi-clipboard-data me-2"></i>Rekap Presensi
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('siswa.jadwal') }}" 
                   class="nav-link {{ request()->routeIs('siswa.jadwal') ? 'active' : '' }}">
                   <i class="bi bi-calendar-week me-2"></i>Jadwal
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('siswa.izin') }}" 
                   class="nav-link {{ request()->routeIs('siswa.izin') ? 'active' : '' }}">
                   <i class="bi bi-envelope-paper me-2"></i>Pengajuan Izin
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('siswa.presensi') }}" 
                   class="nav-link {{ request()->routeIs('siswa.presensi') ? 'active' : '' }}">
                   <i class="bi bi-camera-video me-2"></i>Presensi
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('siswa.profile') }}" 
                   class="nav-link {{ request()->routeIs('siswa.profile') ? 'active' : '' }}">
                   <i class="bi bi-person-lines-fill me-2"></i>Profil
                </a>
            </li>
        </ul>
    </div>

    <!-- Sidebar Mobile (Offcanvas) -->
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="sidebarMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('siswa.dashboard') }}" 
                       class="nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                       <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('siswa.rekap') }}" 
                       class="nav-link {{ request()->routeIs('siswa.rekap') ? 'active' : '' }}">
                       <i class="bi bi-clipboard-data me-2"></i>Rekap Presensi
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('siswa.jadwal') }}" 
                       class="nav-link {{ request()->routeIs('siswa.jadwal') ? 'active' : '' }}">
                       <i class="bi bi-calendar-week me-2"></i>Jadwal
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('siswa.izin') }}" 
                       class="nav-link {{ request()->routeIs('siswa.izin') ? 'active' : '' }}">
                       <i class="bi bi-envelope-paper me-2"></i>Pengajuan Izin
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('siswa.presensi') }}" 
                       class="nav-link {{ request()->routeIs('siswa.presensi') ? 'active' : '' }}">
                       <i class="bi bi-camera-video me-2"></i>Presensi
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('siswa.profile') }}" 
                       class="nav-link {{ request()->routeIs('siswa.profile') ? 'active' : '' }}">
                       <i class="bi bi-person-lines-fill me-2"></i>Profil
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content py-4">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h4>@yield('page-title')</h4>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
