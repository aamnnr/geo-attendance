<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('liburs', function (Blueprint $table) {
            $table->id('libur_id');
            $table->string('keterangan', 100);
            $table->date('libur_dari');
            $table->date('libur_sampai');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('liburs');
    }
};
