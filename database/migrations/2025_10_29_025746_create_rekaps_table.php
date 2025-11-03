<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekaps', function (Blueprint $table) {
            $table->id('rekap_id');
            
            // Relasi ke siswa
            $table->unsignedBigInteger('siswa_id');
            $table->foreign('siswa_id')
                  ->references('siswa_id')
                  ->on('siswas')
                  ->onDelete('cascade');

            // Data waktu & status
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();

            // Status presensi (hadir, alpa, izin, terlambat, pulang cepat)
            $table->enum('status', ['hadir', 'alpa', 'izin', 'terlambat', 'pulang cepat'])
                  ->default('hadir');

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();

            // Mencegah duplikasi rekap di tanggal yang sama untuk siswa yang sama
            $table->unique(['siswa_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekaps');
    }
};
