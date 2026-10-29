<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id('siswa_id');
            $table->foreignId('kelas_id')->constrained('kelas', 'kelas_id')->cascadeOnDelete();
            $table->string('siswa_nama', 50);
            $table->string('siswa_nis', 20)->unique();
            $table->string('siswa_password');
            $table->string('siswa_email', 100)->nullable()->unique();
            $table->string('siswa_hp', 20)->nullable();
            $table->text('siswa_alamat')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
