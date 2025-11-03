<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('tipe'); // contoh: 'alpa_otomatis', 'login_admin', dll
            $table->text('pesan');  // contoh: '10 siswa ditandai alpa pada 2025-10-29'
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('logs');
    }
};
