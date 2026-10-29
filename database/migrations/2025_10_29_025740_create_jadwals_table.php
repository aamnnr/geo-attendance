<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id('jadwal_id');
            $table->foreignId('kelas_id')->constrained('kelas', 'kelas_id')->cascadeOnDelete();
            $table->string('mapel', 100);
            $table->string('guru', 100)->nullable();
            $table->string('hari', 10);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();

            $table->index(['kelas_id', 'hari']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
