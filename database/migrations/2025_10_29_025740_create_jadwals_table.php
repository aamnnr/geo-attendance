<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id('jadwal_id');
            $table->unsignedBigInteger('kelas_id'); // FK manual

            // 🔗 relasi ke kelas.kelas_id
            $table->foreign('kelas_id')
                ->references('kelas_id')
                ->on('kelas')
                ->onDelete('cascade');

            $table->string('mapel', 100)->nullable();

            $table->string('hari', 20);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('jadwals');
    }
};
