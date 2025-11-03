<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('izins', function (Blueprint $table) {
            // Menambahkan kolom untuk menyimpan path file bukti
            // Dibuat nullable() karena bukti bersifat opsional
            // Ditempatkan setelah 'izin_sampai' agar rapi di struktur DB
            $table->string('bukti')->nullable()->after('izin_sampai');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('izins', function (Blueprint $table) {
            // Menghapus kolom jika migrasi di-rollback
            $table->dropColumn('bukti');
        });
    }
};

