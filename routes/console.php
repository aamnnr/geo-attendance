<?php

use Illuminate\Support\Facades\Schedule;

// Tandai Alpa otomatis setiap hari pukul 23:59 (jalankan `php artisan schedule:work`).
Schedule::command('rekap:alpa')->dailyAt('23:59');
