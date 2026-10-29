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
            $table->foreignId('siswa_id')->constrained('siswas', 'siswa_id')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins', 'admin_id')->nullOnDelete();
            // Lihat App\Enums\JenisIzin & App\Enums\StatusIzin.
            $table->string('jenis', 20)->default('izin');
            $table->string('keterangan');
            $table->date('izin_dari');
            $table->date('izin_sampai');
            $table->string('bukti')->nullable();
            $table->string('status', 20)->default('pending');
            $table->string('catatan_admin')->nullable();
            $table->timestamp('diproses_pada')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izins');
    }
};
