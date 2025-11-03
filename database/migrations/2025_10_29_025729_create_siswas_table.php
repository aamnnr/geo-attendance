<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id('siswa_id');
            $table->string('siswa_nama', 50);
            $table->string('siswa_nis', 20)->unique();
            $table->unsignedBigInteger('kelas_id');
            
            // Ini adalah satu-satunya kolom password yang benar
            $table->string('siswa_password', 255);

            // --- KOLOM BARU YANG DITAMBAHKAN ---
            $table->string('siswa_email', 100)->unique()->nullable();
            $table->string('siswa_hp', 20)->nullable();
            $table->text('siswa_alamat')->nullable();
            
            // 🔗 Foreign key
            $table->foreign('kelas_id')
                  ->references('kelas_id')
                  ->on('kelas') // Pastikan nama tabel kelas Anda 'kelas'
                  ->onDelete('cascade');

            $table->timestamps();
            
            // Kolom 'password' yang duplikat sudah dihapus
        });
    }

    public function down(): void {
        Schema::dropIfExists('siswas');
    }
};
