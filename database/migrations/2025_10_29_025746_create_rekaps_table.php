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
            $table->foreignId('siswa_id')->constrained('siswas', 'siswa_id')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            // Lihat App\Enums\StatusPresensi untuk daftar nilai yang valid.
            $table->string('status', 20)->default('hadir');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude_keluar', 10, 7)->nullable();
            $table->decimal('longitude_keluar', 10, 7)->nullable();
            $table->unsignedInteger('jarak')->nullable()->comment('Jarak dari sekolah saat presensi masuk (meter)');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            // Satu siswa hanya punya satu rekap per tanggal.
            $table->unique(['siswa_id', 'tanggal']);
            $table->index(['tanggal', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekaps');
    }
};
