<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izins', function (Blueprint $table) {
            $table->id('izin_id');
            $table->unsignedBigInteger('siswa_id'); // FK ke tabel siswas
            $table->unsignedBigInteger('admin_id')->nullable(); // FK opsional ke admin

            $table->string('keterangan', 100); // alasan izin
            $table->date('izin_dari'); // mulai tanggal izin
            $table->date('izin_sampai'); // selesai tanggal izin

            // status izin: pending, disetujui, atau ditolak
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');

            $table->timestamps();

            // 🔗 relasi foreign key
            $table->foreign('siswa_id')
                ->references('siswa_id')
                ->on('siswas')
                ->onDelete('cascade');

            $table->foreign('admin_id')
                ->references('admin_id')
                ->on('admins')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izins');
    }
};
