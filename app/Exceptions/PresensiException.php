<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Dilempar ketika presensi ditolak karena aturan bisnis (di luar radius, libur, dsb).
 * Pesannya aman ditampilkan langsung ke siswa.
 */
class PresensiException extends RuntimeException {}
